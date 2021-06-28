<?php
namespace ExEss\Cms\Api\V8_Custom\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use ExEss\Cms\Base\Request\AbstractRequest;
use ExEss\Cms\Api\V8_Custom\Repository\Request\AuditRequest;
use ExEss\Cms\Api\V8_Custom\Repository\Response\AuditList;
use ExEss\Cms\Base\Response\Pagination;
use JsonMapper;

class AuditRepository extends AbstractRepository
{
    private EntityManagerInterface $em;

    private JsonMapper $jsonMapper;

    public function __construct(EntityManagerInterface $em, JsonMapper $jsonMapper)
    {
        $this->em = $em;
        $this->jsonMapper = $jsonMapper;
    }

    /**
     * @inheritdoc
     */
    public function findBy(array $requestData)
    {
        $request = $this->getRequest($requestData);

        $auditObject = new \stdClass();
        $auditObject->audits = $this->getRows($request);
        $auditObject->pagination = $this->getPagination($request);

        $auditList = new AuditList();

        $this->jsonMapper->bStrictNullTypes = false;
        $this->jsonMapper->bEnforceMapType = false;

        return $this->jsonMapper->map($auditObject, $auditList);
    }

    public function getRequest(array $requestData): AbstractRequest
    {
        return AuditRequest::createFrom($requestData);
    }

    private function getRows(AuditRequest $request): array
    {
        $metadata = $this->em->getClassMetadata($request->getRecordType());

        return $this->retrieve(
            $metadata,
            $this->getAuditedFields($metadata),
            $request->getRecordId(),
            $request->getFilters(),
            ($request->getPage() - 1) * $request->getLimit(),
            $request->getLimit()
        );
    }

    private function getWhereConditions(array $filters): array
    {
        $conditions = ["1=1"];
        $parameters = [];

        foreach ($filters as $fieldKey => $fieldConditions) {
            foreach ($fieldConditions as $condition) {
                if (!isset($condition['value'])
                    || $condition['value'] === ''
                    || $condition['value'] === null
                    || (\is_array($condition['value']) && empty($condition['value']))
                ) {
                    continue;
                }

                switch ($condition['operator']) {
                    case 'IN':
                        $conditions[] = $fieldKey . ' IN (' . $condition['value'] . ')';
                        break;
                    case '<':
                    case '>':
                    case '>=':
                    case '<=':
                        $parameters[$fieldKey] = $condition['value'];
                        $conditions[] = "$fieldKey {$condition['operator']} :$fieldKey";
                        break;
                    case '=':
                    default:
                        $allValues = \explode(';', $condition['value']);
                        $clauses = [];
                        foreach ($allValues as $value) {
                            $parameters[$fieldKey] = $value . '%';
                            $clauses[] = "$fieldKey LIKE :$fieldKey";
                        }

                        $conditions[] = '(' . \implode(' OR ', $clauses) . ')';
                        break;
                }
            }
        }

        return [
            $conditions,
            $parameters,
        ];
    }

    private function getPagination(AuditRequest $request): Pagination
    {
        $numOfRows = $this->em->getConnection()->fetchAllAssociative('SELECT FOUND_ROWS()');
        $total = (int) \current(\array_pop($numOfRows));

        return new Pagination(
            $request->getPage(),
            $request->getLimit(),
            $total,
            \ceil($total / $request->getLimit())
        );
    }

    private function getAuditedFields(ClassMetadata $metadata): array
    {
        return \array_filter(
            \array_keys($metadata->fieldNames),
            function (string $field) use ($metadata) {
                return !\in_array($field, ['date_modified', 'modified_user_id', 'created_by'], true);
            }
        );
    }

    private function retrieve(
        ClassMetadata $metadata,
        array $fields,
        string $id,
        array $filters,
        int $offset,
        int $limit
    ): array {
        $table = $metadata->getTableName() . '_aud';
        [$whereConditions, $parameters] = $this->getWhereConditions($filters);
        $whereConditions = \implode(' AND ', $whereConditions);

        $selectFields = \implode(
            ", ",
            \array_map(
                function (string $field) {
                    return \sprintf(
                        'IFNULL(newRecord.%1$s, "") as %1$s, IFNULL(oldRecord.%1$s, "") as %1$s_old',
                        $field
                    );
                },
                $fields
            )
        );

        $concat = \implode(
            ", ",
            \array_map(
                function ($field) use ($metadata) {
                    return \sprintf(
                        '
                            IF (
                                aud.%1$s <> aud.%1$s_old 
                                AND (aud.%1$s <> "" OR aud.%1$s_old <> ""),
                                IF(
                                    aud.audit_operation = "INSERT",
                                    CONCAT("<b>%1$s</b> (%2$s): ", aud.%1$s, "<br>"),
                                    CONCAT(
                                        "<b>%1$s</b>(varchar): ",
                                        IF(aud.%1$s_old <> "", aud.%1$s_old, "<i>empty</i>" ), 
                                        " -> ", 
                                        IF(aud.%1$s <> "", aud.%1$s, "<i>empty</i>")
                                        , "<br>"
                                        )
                                    )
                                , 
                                ""
                            )
                        ',
                        $field,
                        $metadata->getTypeOfField($metadata->fieldNames[$field])
                    );
                },
                $fields
            )
        );

        $query = "
            SELECT
                aud.audit_timestamp as timestamp,
                aud.audit_operation as operation,
                u.user_name as username,
                CONCAT($concat) as changes
            FROM (
                SELECT
                    IF(newRecord.audit_operation = 'INSERT', newRecord.created_by, newRecord.modified_user_id) 
                      as aud_user_id,
                    newRecord.audit_timestamp,
                    newRecord.audit_operation,
                    $selectFields
                FROM (
                    SELECT 
                        record.id, 
                        record.audit_timestamp as newRecordTimeStamp,
                        (
                            SELECT oldRecord.audit_timestamp
                            FROM $table oldRecord
                            WHERE oldRecord.id = record.id AND oldRecord.audit_timestamp < record.audit_timestamp
                            ORDER BY oldRecord.audit_timestamp DESC
                            LIMIT 1
                        ) as oldRecordTimeStamp
                    FROM $table record
                    WHERE record.id = '$id'
                ) AS timeStamps
                LEFT JOIN $table as newRecord ON 
                    newRecord.id = timeStamps.id 
                    AND newRecord.audit_timestamp = timeStamps.newRecordTimeStamp
                LEFT JOIN $table as oldRecord ON 
                    oldRecord.id = timeStamps.id 
                    AND oldRecord.audit_timestamp = timeStamps.oldRecordTimeStamp
                     
            ) as aud
            LEFT JOIN users as u ON u.id = aud.aud_user_id
            HAVING 
                (
                    aud.audit_operation IN ('INSERT', 'DELETE')
                    OR (
                        aud.audit_operation = 'UPDATE'
                        AND changes <> ''
                    )
                )
                AND $whereConditions
            LIMIT $offset,$limit
          ";

        return $this->em->getConnection()->fetchAllAssociative($query, $parameters);
    }
}
