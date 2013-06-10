<?php
/**
 * Copyright 2010-2013 Amazon.com, Inc. or its affiliates. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may not use this file except in compliance with the License.
 * A copy of the License is located at
 *
 * http://aws.amazon.com/apache2.0
 *
 * or in the "license" file accompanying this file. This file is distributed
 * on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either
 * express or implied. See the License for the specific language governing
 * permissions and limitations under the License.
 */

namespace Aws\DynamoDb_2011_12_05\Enum;

use Aws\Common\Enum;

/**
 * Contains enumerable TableStatus values
 */
class TableStatus extends Enum
{
    const CREATING = 'CREATING';
    const UPDATING = 'UPDATING';
    const DELETING = 'DELETING';
    const ACTIVE = 'ACTIVE';
}
