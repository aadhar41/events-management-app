<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Event Management Api Documentation",
 *     description="<b>Api Documentation For</b>, Services responsible for getting the list of all events, get a single event details, creating, updating & deleting an event in database as well as attendee module i.e. getting all attendees for an event, attend an event, get details of an event's attendee & unAttend an Event.",
 *     @OA\Contact(
 *         name="Aadhar gaur",
 *         email="aadhar41@gmail.com"
 *     ),
 *     @OA\License(
 *         name="Apache 2.0",
 *         url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *     )
 * ),
 * @OA\Server(
 *     url="http://events-management-app.test/api/",
 * ),
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
