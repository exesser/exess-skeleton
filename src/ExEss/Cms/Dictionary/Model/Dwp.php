<?php

namespace ExEss\Cms\Dictionary\Model;

class Dwp
{
    public const DWP = 'dwp';
    public const PREFIX = self::DWP . '|';
    public const DYNAMIC_LOADED_FIELDS = self::PREFIX . 'dynamicLoadedFields';
    public const BINARY_FILE = self::PREFIX . 'binaryFile';
    public const CRUD_DUPLICATE_RECORD_ID = self::PREFIX . 'duplicateRecordId';
    public const ROW_OPTIONS_PREFIX = self::PREFIX . 'rowOptions';
    public const ROW_OPTIONS_MODEL_KEY = self::ROW_OPTIONS_PREFIX . '|modelKey';
    public const ROW_OPTIONS_REPEATS_BY = self::ROW_OPTIONS_PREFIX . '|repeatsBy';
    public const ROW_OPTIONS_MODEL_ID = self::ROW_OPTIONS_PREFIX . '|modelId';
    public const CACHE_KEY = self::PREFIX . 'cache_key';
    public const PARENT_TYPE = self::PREFIX . 'parentType';
    public const PARENT_ID = self::PREFIX . 'parentId';
    public const RECORD_TYPE = self::PREFIX . 'recordType';
    public const RELATION_NAME = self::PREFIX . 'relationName';
    public const RECORD_TYPE_OF_RECORD_ID = 'recordTypeOfRecordId';
    // This is used to store the parent model on child model to be able to access it on validation
    // usage: "dwp|parentModel.key|on|parent|model"
    public const PARENT_MODEL = self::PREFIX . 'parentModel';
    public const RELATIONS_FIELD = self::PREFIX . 'dynamicRelation';
    public const FLAG_CONFIRM_ACTION_KEY = self::PREFIX . 'flag|confirmCommandKey';
    public const CONTACT_PERSON_GROUP = self::PREFIX . 'contactPerson';
    public const RETURN_MODULE = self::PREFIX . 'returnModule';
    public const GUIDANCE_FLOW_ID = self::PREFIX . 'guidanceFlowId';
}
