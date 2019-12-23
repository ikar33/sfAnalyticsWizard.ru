<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: google/privacy/dlp/v2beta1/storage.proto

namespace Google\Privacy\Dlp\V2beta1;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * A unique identifier for a Datastore entity.
 * If a key's partition ID or any of its path kinds or names are
 * reserved/read-only, the key is reserved/read-only.
 * A reserved/read-only key is forbidden in certain documented contexts.
 *
 * Generated from protobuf message <code>google.privacy.dlp.v2beta1.Key</code>
 */
class Key extends \Google\Protobuf\Internal\Message
{
    /**
     * Entities are partitioned into subsets, currently identified by a project
     * ID and namespace ID.
     * Queries are scoped to a single partition.
     *
     * Generated from protobuf field <code>.google.privacy.dlp.v2beta1.PartitionId partition_id = 1;</code>
     */
    private $partition_id = null;
    /**
     * The entity path.
     * An entity path consists of one or more elements composed of a kind and a
     * string or numerical identifier, which identify entities. The first
     * element identifies a _root entity_, the second element identifies
     * a _child_ of the root entity, the third element identifies a child of the
     * second entity, and so forth. The entities identified by all prefixes of
     * the path are called the element's _ancestors_.
     * A path can never be empty, and a path can have at most 100 elements.
     *
     * Generated from protobuf field <code>repeated .google.privacy.dlp.v2beta1.Key.PathElement path = 2;</code>
     */
    private $path;

    public function __construct() {
        \GPBMetadata\Google\Privacy\Dlp\V2Beta1\Storage::initOnce();
        parent::__construct();
    }

    /**
     * Entities are partitioned into subsets, currently identified by a project
     * ID and namespace ID.
     * Queries are scoped to a single partition.
     *
     * Generated from protobuf field <code>.google.privacy.dlp.v2beta1.PartitionId partition_id = 1;</code>
     * @return \Google\Privacy\Dlp\V2beta1\PartitionId
     */
    public function getPartitionId()
    {
        return $this->partition_id;
    }

    /**
     * Entities are partitioned into subsets, currently identified by a project
     * ID and namespace ID.
     * Queries are scoped to a single partition.
     *
     * Generated from protobuf field <code>.google.privacy.dlp.v2beta1.PartitionId partition_id = 1;</code>
     * @param \Google\Privacy\Dlp\V2beta1\PartitionId $var
     * @return $this
     */
    public function setPartitionId($var)
    {
        GPBUtil::checkMessage($var, \Google\Privacy\Dlp\V2beta1\PartitionId::class);
        $this->partition_id = $var;

        return $this;
    }

    /**
     * The entity path.
     * An entity path consists of one or more elements composed of a kind and a
     * string or numerical identifier, which identify entities. The first
     * element identifies a _root entity_, the second element identifies
     * a _child_ of the root entity, the third element identifies a child of the
     * second entity, and so forth. The entities identified by all prefixes of
     * the path are called the element's _ancestors_.
     * A path can never be empty, and a path can have at most 100 elements.
     *
     * Generated from protobuf field <code>repeated .google.privacy.dlp.v2beta1.Key.PathElement path = 2;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * The entity path.
     * An entity path consists of one or more elements composed of a kind and a
     * string or numerical identifier, which identify entities. The first
     * element identifies a _root entity_, the second element identifies
     * a _child_ of the root entity, the third element identifies a child of the
     * second entity, and so forth. The entities identified by all prefixes of
     * the path are called the element's _ancestors_.
     * A path can never be empty, and a path can have at most 100 elements.
     *
     * Generated from protobuf field <code>repeated .google.privacy.dlp.v2beta1.Key.PathElement path = 2;</code>
     * @param \Google\Privacy\Dlp\V2beta1\Key_PathElement[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setPath($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::MESSAGE, \Google\Privacy\Dlp\V2beta1\Key_PathElement::class);
        $this->path = $arr;

        return $this;
    }

}

