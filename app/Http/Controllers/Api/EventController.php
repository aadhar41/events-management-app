<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Http\Traits\CanLoadRelationships;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class EventController extends Controller
{
    use CanLoadRelationships;

    private array $relations = ['user', 'attendees', 'attendees.user'];

    public function __construct($var = null)
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
        $this->middleware('throttle:api')->only(['store', 'update', 'destroy']);
        $this->authorizeResource(Event::class, 'event');
    }

    /**
     * @OA\Get(
     *    path="/events",
     *    operationId="getEventList",
     *    tags={"Events"},
     *    summary="Get list of all events",
     *    description="Get list of all events",
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
     *                 ),
     *                  @OA\Items(
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
    public function index()
    {
        $query = $this->loadRelationships(Event::query());
        return EventResource::collection($query->latest()->paginate());
    }



    /**
     * @OA\Post(
     *      path="/events?include=user,attendees",
     *      operationId="createEvent",
     *      tags={"Events"},
     *      summary="Store/Create event in DB",
     *      description="Store/Create event in DB",
     *      @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={"name", "start_time", "end_time"},
     *            @OA\Property(property="name", type="string", format="string", example="Test event Title"),
     *            @OA\Property(property="start_time", type="string", format="string", example="2023-12-01 15:00:00"),
     *            @OA\Property(property="end_time", type="string", format="string", example="2023-12-31 15:00:00"),
     *         ),
     *          @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"name", "start_time", "end_time"},
     *               @OA\Property(property="name", type="string", format="string", example="Test event Title"),
     *               @OA\Property(property="start_time", type="string", format="string", example="2023-12-01 15:00:00"),
     *               @OA\Property(property="end_time", type="string", format="string", example="2023-12-31 15:00:00"),
     *            ),
     *        ),
     *      ),
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
    public function store(Request $request)
    {
        $event = Event::create([
            ...$request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'start_time' => 'required|date',
                'end_time' => 'required|date|after:start_time',
            ]),
            'user_id' => $request->user()->id
        ]);
        return new EventResource($this->loadRelationships($event));
    }


    /**
     * @OA\Get(
     *    path="/events/{event}",
     *    operationId="getEventDetails",
     *    tags={"Events"},
     *    summary="Get an Event Details",
     *    description="Get an Event Details",
     *    @OA\Parameter(name="event", in="path", description="Id of Event", required=true,
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
     *       )
     *  )
     */
    public function show(Event $event)
    {
        return new EventResource($this->loadRelationships($event));
    }

    /**
     * Update the specified resource in storage.
     */
    /**
     * @OA\Put(
     *     path="/events/{event}",
     *     operationId="updateEvent",
     *     tags={"Events"},
     *     summary="Update event in DB",
     *     description="Update event in DB",
     *     @OA\Parameter(name="event", in="path", description="Id of Event", required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *        required=true,
     *        @OA\JsonContent(
     *           required={"name"},
     *            @OA\Property(property="name", type="string", format="string", example="Test event Title"),
     *        ),
     *     ),
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
    public function update(Request $request, Event $event)
    {
        // if (Gate::denies('update-event', $event)) {
        //     abort(403, 'You are not authorized to update this event.');
        // }

        // $this->authorize('update-event', $event);

        $event->update(
            $request->validate([
                'name' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'start_time' => 'sometimes|date',
                'end_time' => 'sometimes|date|after:start_time',
            ]),
        );
        return new EventResource($this->loadRelationships($event));
    }

    /**
     * @OA\Delete(
     *    path="/events/{event}",
     *    operationId="removeEvent",
     *    tags={"Events"},
     *    summary="Delete Event",
     *    description="Delete Event",
     *    @OA\Parameter(name="event", in="path", description="Id of Event", required=true,
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
    public function destroy(Event $event)
    {
        $event->delete();
        return response(status: 204);
    }
}
