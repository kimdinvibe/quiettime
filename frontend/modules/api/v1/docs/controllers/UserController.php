<?php

namespace frontend\modules\api\v1\docs\controllers;
    
require_once '_define.php';
 
/**
 * @api {get} /user/auth-by-access-token/ Auth user by access token
 * @apiVersion 0.1.0
 * @apiName UserAuthByAccessToken
 * @apiGroup User
 *
 * @apiUse HeaderBase
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *               "id": 40,
 *               "username": "9c7881b6555f4f0f54db0aa8fe485dd2",
 *               "created_at": 1514514481,
 *               "email": "567da9d8d95a2dc8b1f3467659defa99@system.com",
 *               "updated_at": 1514514481,
 *               "firstname": null,
 *               "middlename": null,
 *               "lastname": null,
 *               "fio": "",
 *               "device_id": "kIrwhfKTYBarVIV6qzoRnFuSs0td7aIi",
 *               "avatar": null,
 *               "is_line": 1,
 *               "access_token": "O6NCTMkwxjRJYOeWa5xEzXDEyBR1il5N753Dn00B"
 *      }
 *
 * @apiError UserNotFound User was not found.
 *
 * @apiUse NotFoundError
 *
 * @apiSampleRequest /user/auth-by-access-token/
 */

/**
 * @api {post} /user/registration/ User registration
 * @apiVersion 0.1.0
 * @apiName UserRegistration
 * @apiGroup User
 * 
 * @apiUse HeaderBaseWithoutAuth
 * @apiHeader {String} Content-Type =application/x-www-form-urlencoded Content type.
 *
 * @apiParam (user) {Integer} [is_line] Users from LINE.
 * 
 * @apiParamExample {json} Request-Without-Login-Password:
 *      {
 *          "user": {
 *              "is_line":1
 *          }
 *      }
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *               "id": 40,
 *               "username": "9c7881b6555f4f0f54db0aa8fe485dd2",
 *               "created_at": 1514514481,
 *               "email": "567da9d8d95a2dc8b1f3467659defa99@system.com",
 *               "updated_at": 1514514481,
 *               "firstname": null,
 *               "middlename": null,
 *               "lastname": null,
 *               "fio": "",
 *               "device_id": "kIrwhfKTYBarVIV6qzoRnFuSs0td7aIi",
 *               "avatar": null,
 *               "is_line": 1,
 *               "access_token": "O6NCTMkwxjRJYOeWa5xEzXDEyBR1il5N753Dn00B"
 *      }
 *
 * @apiError ServerError Server error.
 *
 * @apiSampleRequest /user/registration/
*/

/**
 * @api {post} /user/feedback/ User feedback
 * @apiVersion 0.1.0
 * @apiName UserFeedback
 * @apiGroup User
 *
 * @apiUse HeaderBase
 * @apiHeader {String} Content-Type =application/x-www-form-urlencoded Content type.
 *
 * @apiParam {Integer} location_id Location Id.
 * @apiParam {Integer} event_id Event Id.
 * @apiParam {Integer} [rating] Rating(from 1-5).
 * @apiParam {Integer} [sticker_id] Sticker Id.
 *
 * @apiParamExample {json} Request:
        {
            "location_id": 2,
            "event_id": 1,
            "rating": 2,
            "sticker_id": 4
        }
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
        {
            "id": 8
        }
 *
 * @apiError UserNotFound User was not found.
 *
 * @apiUse NotFoundError
 *
 * @apiSampleRequest /user/feedback/
 */