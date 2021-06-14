<?php declare(strict_types=1);

namespace ExEss\Cms\DASH_Dashboard;

use ExEss\Cms\Entity\Property;

class DashboardCalcFunctions
{
    private array $cachedCalculatedValues;

    public function handleDefineState(string $id, array $params, ?string $keyName): string
    {
        if (!isset($this->cachedCalculatedValues[$params[0]])) {
            $this->cachedCalculatedValues[$params[0]] = $this->{"handle" . $params[0]}($id, $params, $keyName);
        }
        $retValue = $this->cachedCalculatedValues[$params[0]] === 0 ? true : false;
        $retValue = $params[1] === "warning" ? !$retValue : $retValue;

        return $retValue ? "true" : "false";
    }

    /**
     * @throws \DomainException When a configured method is not set.
     */
    public function handleProperty(Property $property, string $recordId): string
    {
        $params = \explode(':', $property->getName());
        \array_shift($params);
        $nameParts = \explode(':', $property->getName(), 2);
        $keyName = \array_pop($nameParts);
        if (isset($this->cachedCalculatedValues[$keyName])) {
            $replaceVar = $this->cachedCalculatedValues[$keyName];
        } else {
            $funcName = 'handle' . \ucfirst(\array_pop($params));
            if (!\method_exists($this, $funcName)) {
                throw new \DomainException('Configured method "' . $funcName . '" not set');
            }
            $replaceVar = $this->$funcName($recordId, $params, $keyName);
            $this->cachedCalculatedValues[$keyName] = $replaceVar;
        }

        return $replaceVar;
    }
}
