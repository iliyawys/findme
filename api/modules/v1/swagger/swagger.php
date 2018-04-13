<?php

namespace api\modules\v1\swagger;
/**
 * @SWG\Swagger(
 *     schemes={"http"},
 *     host="109.120.158.33",
 *     basePath="/",
 *     @SWG\Info(
 *         version="1.0.0",
 *         title="Findme",
 *         description="Version: __1.0.0__",
 *         @SWG\Contact(name = "lichunqiang", email = "email@gmail.com")
 *     ),
 * )
 *
 * @SWG\Tag(
 *   name="user",
 *   description="desc",
 *   @SWG\ExternalDocumentation(
 *     description="Find out more about our store",
 *     url="http://swagger.io"
 *   )
 * )
 */
/**
 * @SWG\Definition(
 *   @SWG\Xml(name="##default")
 * )
 */
class ApiResponse
{
    /**
     * @SWG\Property(format="int32", description = "code of result")
     * @var int
     */
    public $code;
    /**
     * @SWG\Property
     * @var string
     */
    public $type;
    /**
     * @SWG\Property
     * @var string
     */
    public $message;
    /**
     * @SWG\Property(format = "int64", enum = {1, 2})
     * @var integer
     */
    public $status;
}