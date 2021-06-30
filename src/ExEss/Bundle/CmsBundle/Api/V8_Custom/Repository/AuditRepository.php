<?php
namespace ExEss\Bundle\CmsBundle\Api\V8_Custom\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use ExEss\Bundle\CmsBundle\Base\Request\AbstractRequest;
use ExEss\Bundle\CmsBundle\Api\V8_Custom\Repository\Request\AuditRequest;
use ExEss\Bundle\CmsBundle\Api\V8_Custom\Repository\Response\AuditList;
use ExEss\Bundle\CmsBundle\Base\Response\BaseListResponse;
use ExEss\Bundle\CmsBundle\Base\Response\Pagination;
use JsonMapper;

class AuditRepository extends AbstractRepository
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @inheritdoc
     */
    public function findBy(array $requestData): BaseListResponse
    {
        $request = $this->getRequest($requestData);

        $metadata = $this->em->getClassMetadata($request->getRecordType());

        $auditObject = new \stdClass();
        $auditObject->audits = $this->getRows($metadata, $request);
        $auditObject->pagination = $this->getPagination($metadata, $request);

        $auditList = new AuditList();

        $mapper = new JsonMapper();
        $mapper->bIgnoreVisibility = true;
        $mapper->bStrictNullTypes = false;
        $mapper->bEnforceMapType = false;

        return $mapper->map($auditObject, $auditList);
    }

    public function getRequest(array $requestData): AbstractRequest
    {
        return AuditRequest::createFrom($requestData);
    }

    private function getRows(ClassMetadata $metadata, AuditRequest $request): array
    {
        $offset = ($request->getPage() - 1) * $request->getLimit();
        $limit = $request->getLimit();

        return $this->queryAuditTable(
            "aud.audit_timestamp as timestamp, u.user_name as username",
            $metadata,
            $request->getRecordId(),
            $request->getFilters(),
            "LIMIT $offset,$limit",
            "ORDER BY aud.audit_timestamp DESC"
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
            \implode(' AND ', $conditions),
            $parameters,
        ];
    }

    private function getPagination(ClassMetadata $metadata, AuditRequest $request): Pagination
    {
        $total = $this->queryAuditTable(
            "COUNT(*) as cnt",
            $metadata,
            $request->getRecordId(),
            $request->getFilters()
        )[0]['cnt'];

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

    private function queryAuditTable(
        string $select,
        ClassMetadata $metadata,
        string $id,
        array $filters,
        string $limit = '',
        string $order = ''
    ): array {
        $table = $metadata->getTableName() . '_aud';
        [$where, $parameters] = $this->getWhereConditions($filters);
        $fields = $this->getAuditedFields($metadata);

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
        $from = $this->getFrom($id, $table, $fields);

        $query = "
            SELECT             
                aud.audit_operation as operation,
                CONCAT($concat) as changes,
                $select
            FROM ($from) as aud
            LEFT JOIN users as u ON u.id = aud.aud_user_id
            HAVING 
                (
                    aud.audit_operation IN ('INSERT', 'DELETE')
                    OR (
                        aud.audit_operation = 'UPDATE'
                        AND changes <> ''
                    )
                )
                AND $where
            $order
            $limit
          ";

        return $this->em->getConnection()->fetchAllAssociative($query, $parameters);
    }

    private function getFrom(string $id, string $table, array $fields): string
    {
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

        return "
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
        ";
    }
}
