<?php

namespace frontend\modules\api\v1\docs\controllers;
    
/**
 * @apiDefine NotFoundError
 *
 * @apiError NotFound The Object was not found.
 *
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 404 Not Found
 *     {
 *          "name": "Not Found",
 *          "message": "",
 *          "code": 0,
 *          "status": 404,
 *          "type": "yii\\web\\NotFoundHttpException"
 *     }
 */
 
/**
 * @apiDefine HeaderBaseWithoutAuth
 *
 * @apiHeader {String} Content-Type =application/json Content type.
 * @apiHeader {String} Device-Type ="Android"  Device Type.
 * @apiHeader {String} Device-ID ="xxxxxxx" Device Id.
 * @apiHeader {String} Device-Name ="Android Test" Device name.
 *
 * @apiHeaderExample {json} Header-Example:
 *     {
 *       "Content-Type": "application/json",
 *       "Device-Type": "Android"
 *       "Device-ID": "564f77d3 c1d06866 77a907d5 31d0450a 9cb552ec 5e2373e5 f2cc2c20 ff013232"
 *       "Device-Name": "Android Test"
 *     }
 */

/**
 * @apiDefine HeaderBase
 *
 * @apiHeader {String} Content-Type =application/json Content type.
 * @apiHeader {String} Authorization ="Bearer xxxxxxxxxxxxxxx" User token.
 * @apiHeader {String} Device-Type ="Android"  Device Type.
 * @apiHeader {String} Device-ID ="xxxxxxx" Device Id.
 * @apiHeader {String} Device-Name ="Android Test" Device name.
 *
 * @apiHeaderExample {json} Header-Example:
 *     {
 *       "Content-Type": "application/json",
 *       "Authorization": "Bearer token",
 *       "Device-Type": "Android"
 *       "Device-ID": "564f77d3 c1d06866 77a907d5 31d0450a 9cb552ec 5e2373e5 f2cc2c20 ff013232"
 *       "Device-Name": "Android Test"
 *     }
 */
 
 /**
 * @apiDefine SuccessResponsePager
 *
 * @apiSuccess (Success 201 Header) {Number} X-Pagination-Current-Page X-Pagination-Current-Page
 * @apiSuccess (Success 201 Header) {Number} X-Pagination-Page-Count X-Pagination-Page-Count
 * @apiSuccess (Success 201 Header) {Number} X-Pagination-Per-Page X-Pagination-Per-Page
 * @apiSuccess (Success 201 Header) {Number} X-Pagination-Total-Count X-Pagination-Total-Count
 * @apiSuccess (Success 201 Header) {text} Link Link
 *  
 */