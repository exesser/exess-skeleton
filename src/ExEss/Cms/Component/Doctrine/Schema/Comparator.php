<?php declare(strict_types=1);

namespace ExEss\Cms\Component\Doctrine\Schema;

use Doctrine\DBAL\Schema\Comparator as BaseComparator;
use Doctrine\DBAL\Schema\Schema;

/**
 * Not added to container
 */
final class Comparator extends BaseComparator
{
    private BaseComparator $comparator;

    private bool $reverse;

    public function __construct(bool $reverse = false)
    {
        $this->comparator = new BaseComparator();
        $this->reverse = $reverse;
    }

    public function compare(Schema $fromSchema, Schema $toSchema): SchemaDiff
    {
        /** @var \ExEss\Cms\Component\Doctrine\Schema\Schema $fromSchema */
        /** @var \ExEss\Cms\Component\Doctrine\Schema\Schema $toSchema */

        $diff = $this->comparator->compare($fromSchema, $toSchema);

        $triggers = [];
        $removedTriggers = [];
        /** @var Trigger $trigger */
        foreach ($toSchema->getTriggers() as $trigger) {
            /** @var Trigger|null $oldTrigger */
            $oldTrigger = $fromSchema->getTriggers()[$trigger->getName()] ?? null;
            if (!$oldTrigger) {
                $triggers[] = $trigger;
            } elseif (!$trigger->equals($oldTrigger)) {
                $removedTriggers[] = $oldTrigger;
                $triggers[] = $trigger;
            }
        }

        foreach ($fromSchema->getTriggers() as $trigger) {
            if (!isset($toSchema->getTriggers()[$trigger->getName()])) {
                $removedTriggers[] = $trigger;
            }
        }

        $inserts = [];
        $reverseInserts = [];
        foreach ($toSchema->getInserts() as $insert) {
            $handled = false;
            foreach ($fromSchema->getInserts() as $fromInsert) {
                if (!$insert->equals($fromInsert)) {
                    continue;
                }

                $insertDiff = $insert->diff($fromInsert, $this->reverse);
                $fromDiff = $fromInsert->diff($insert, $this->reverse);
                if (!$insertDiff instanceof Insert && !$fromDiff instanceof Insert) {
                    $handled = true;
                    continue;
                }

                if ($insertDiff instanceof Insert) {
                    $inserts[] = $insertDiff;
                    $handled = true;
                }

                if ($fromDiff instanceof Insert) {
                    $reverseInserts[] = $fromDiff;
                    $handled = true;
                }
            }

            if (!$handled) {
                $inserts[] = $insert;
            }
        }

        return new SchemaDiff($diff, $triggers, $removedTriggers, $inserts, $reverseInserts);
    }
}
