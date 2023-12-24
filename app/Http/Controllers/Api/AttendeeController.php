<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AttendeeResource;
use App\Http\Traits\CanLoadRelationships;
use App\Models\Attendee;
use App\Models\Event;
use Illuminate\Http\Request;

class AttendeeController extends Controller
{
    use CanLoadRelationships;

    private array $relations = ['user'];

    public function __construct($var = null)
    {
        $this->middleware('auth:sanctum')->except(['index', 'show', 'update']);
        $this->middleware('throttle:api')->only(['store', 'destroy']);
        $this->authorizeResource(Attendee::class, 'attendee');
    }

    /**
     * @OA\Get(
     *    path="/events/{event}/attendees",
     *    operationId="getEventAttendees",
     *    tags={"Attendees"},
     *    summary="API (endpoint) for getting all event attendees.",
     *    description="API (endpoint) for getting all event attendees.",
     *    @OA\Parameter(
     *        name="include",
     *        in="query",
     *        description="relationship data i.e. attendees,user",
     *        required=false,
     *        @OA\Schema(type="string")
     *    ),
     *    @OA\Parameter(
     *        name="page",
     *        in="query",
     *        description="the page number",
     *        required=false,
     *        @OA\Schema(type="integer")
     *    ),
     *     @OA\Response(response="404", description="Error: Not Found. The route events could not be found."),
     *     @OA\Response(
     *          response=200, description="Success",
     *          @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 format="object",
     *                 @OA\Items(
     *                      @OA\Property(
     *                         property="id",
     *                         type="integer",
     *                         example="3"
     *                      ),
     *                      @OA\Property(
     *                         property="user_id",
     *                         type="string",
     *                         example="1"
     *                      ),
     *                      @OA\Property(
     *                         property="event_id",
     *                         type="string",
     *                         example="10"
     *                      ),
     *                      @OA\Property(
     *                         property="created_at",
     *                         type="string",
     *                         example="2023-12-16 13:47:06"
     *                      ),
     *                      @OA\Property(
     *                         property="updated_at",
     *                         type="string",
     *                         example="2024-02-11 21:18:46"
     *                      ),
     *                 ),
     *             )
     *          )
     *     )
     *  )
     */
    public function index(Event $event)
    {
        $attendees = $this->loadRelationships(
            $event->attendees()->latest()
        );

        return AttendeeResource::collection(
            $attendees->paginate()
        );
    }

    /**
     * @OA\Post(
     *      path="/events/10/attendees?include=user,attendees",
     *      operationId="attendEvent",
     *      tags={"Attendees"},
     *      summary="Attend an Event",
     *      description="Attend an Event",
     *    @OA\Parameter(
     *        name="include",
     *        in="query",
     *        description="relationship data i.e. attendees,user",
     *        required=false,
     *        @OA\Schema(type="string")
     *    ),
     *    @OA\Parameter(name="event", in="path", description="Id of Event", required=true,
     *        @OA\Schema(type="integer")
     *    ),
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response="401", description="Unauthenticated."),
     *     @OA\Response(response="404", description="Error: Not Found. The route events could not be found."),
     *    @OA\Response(
     *          response=201, description="Success",
     *          @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 format="object",
     *                 example={
     *                  "id": "3",
     *                  "name": "Aut est minima.",
     *                  "description": "Ipsam iste hic ut repellat totam est esse ad.",
     *                  "start_time": "2023-12-16 13:47:06",
     *                  "end_time": "2024-02-11 21:18:46",
     *                },
     *                 @OA\Items(
     *                      @OA\Property(
     *                         property="id",
     *                         type="integer",
     *                         example="191"
     *                      ),
     *                      @OA\Property(
     *                         property="name",
     *                         type="string",
     *                         example="Aut est minima."
     *                      ),
     *                      @OA\Property(
     *                         property="description",
     *                         type="string",
     *                         example="Ipsam iste hic ut repellat totam est esse ad."
     *                      ),
     *                      @OA\Property(
     *                         property="start_time",
     *                         type="string",
     *                         example="2023-12-16 13:47:06"
     *                      ),
     *                      @OA\Property(
     *                         property="end_time",
     *                         type="string",
     *                         example="2024-02-11 21:18:46"
     *                      ),
     *                 )
     *             )
     *          )
     *     )
     *  )
     */
    public function store(Request $request, Event $event)
    {
        $this->loadRelationships(
            $attendee = $event->attendees()->create([
                'user_id' => $request->user()->id
            ])
        );

        return new AttendeeResource($attendee);
    }

    /**
     * @OA\Get(
     *    path="/events/{event}/attendees/{attendee}",
     *    operationId="getEventAttendee",
     *    tags={"Attendees"},
     *    summary="Get An Event Attendee Details",
     *    description="Get An Event Attendee Details",
     *    @OA\Parameter(name="event", in="path", description="Id of Event", required=true,
     *        @OA\Schema(type="integer")
     *    ),
     *    @OA\Parameter(name="attendee", in="path", description="Id of attendee", required=true,
     *        @OA\Schema(type="integer")
     *    ),
     *     @OA\Response(response="401", description="Unauthenticated."),
     *     @OA\Response(response="404", description="Error: Not Found. The route events could not be found."),
     *     @OA\Response(
     *          response=200, description="Success",
     *          @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 format="object",
     *                 example={
     *                  "id": "3",
     *                  "user_id": "Aut est minima.",
     *                  "event_id": "Ipsam iste hic ut repellat totam est esse ad.",
     *                  "created_at": "2023-12-16 13:47:06",
     *                  "updated_at": "2024-02-11 21:18:46",
     *                },
     *                 @OA\Items(
     *                      @OA\Property(
     *                         property="id",
     *                         type="integer",
     *                         example="148"
     *                      ),
     *                      @OA\Property(
     *                         property="name",
     *                         type="string",
     *                         example="America Stanton"
     *                      ),
     *                      @OA\Property(
     *                         property="email",
     *                         type="string",
     *                         example="burnice.cole@example.org"
     *                      ),
     *                      @OA\Property(
     *                         property="email_verified_at",
     *                         type="string",
     *                         example="2023-12-16 13:47:06"
     *                      ),
     *                      @OA\Property(
     *                         property="created_at",
     *                         type="string",
     *                         example="2023-12-16 13:47:06"
     *                      ),
     *                      @OA\Property(
     *                         property="updated_at",
     *                         type="string",
     *                         example="2024-02-11 21:18:46"
     *                      ),
     *                 )
     *             )
     *          )
     *     )
     *       )
     *  )
     */
    public function show(Event $event, Attendee $attendee)
    {
        return new AttendeeResource(
            $this->loadRelationships($attendee)
        );
    }

    /**
     * @OA\Delete(
     *    path="/events/{event}/attendees/{attendee}",
     *    operationId="unAttendEvent",
     *    tags={"Attendees"},
     *    summary="Unattended an Event",
     *    description="Unattended an Event",
     *    security={{"bearerAuth":{}}},
     *    @OA\Parameter(name="event", in="path", description="Id of Event", required=true,
     *        @OA\Schema(type="integer")
     *    ),
     *    @OA\Parameter(name="attendee", in="path", description="Id of attendee", required=true,
     *        @OA\Schema(type="integer")
     *    ),
     *    @OA\Response(response="401", description="Unauthenticated."),
     *    @OA\Response(response="404", description="Error: Not Found. The route events could not be found."),
     *    @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *          @OA\Property(property="status_code", type="integer", example="200"),
     *          @OA\Property(property="data",type="object")
     *         ),
     *    )
     *      )
     *  )
     */
    public function destroy(Event $event, Attendee $attendee)
    {
        $attendee->delete();
        response(status: 204);
    }
}
