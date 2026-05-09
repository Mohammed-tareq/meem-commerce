<?php

namespace Marvel\Http\Controllers;

use Illuminate\Http\Request;
use Marvel\Database\Repositories\ContactRepository;
use Marvel\Enums\Permission;
use Marvel\Exceptions\MarvelException;
use Marvel\Http\Requests\ContactCreateReplayRequest;
use Marvel\Http\Requests\ContactCreateRequest;
use Marvel\Http\Resources\ContactCollection;
use Marvel\Http\Resources\ContactResource;
use Marvel\Traits\ApiResponse;

class ContactController extends CoreController
{
    use ApiResponse;

    public $repository;

    public function __construct(ContactRepository $repository)
    {
        $this->repository = $repository;
        $this->middleware('permission:' . Permission::VIEW_CONTACTS, ['only' => ['index']]);
        $this->middleware('permission:' . Permission::UPDATE_CONTACT, ['only' => ['show', 'sendReplay']]);
        $this->middleware('permission:' . Permission::DELETE_CONTACT, ['only' => ['destroy', 'deleteAll']]);
    }

    public function index(Request $request)
    {
        $limit = $request->limit ?? 15;
        $read = $request->query('read', false);
        $unread = $request->query('unread', false);
        $replay = $request->query('replay', false);

        $contactsQuery = $this->repository;

        if ($read) {
            $contactsQuery = $contactsQuery->read();
        }
        if ($unread) {
            $contactsQuery = $contactsQuery->unread();
        }
        if ($replay) {
            $contactsQuery = $contactsQuery->replay();
        }

        $contacts = $contactsQuery->paginate($limit);
        $data = new ContactCollection($contacts);

        return $this->apiResponse(FETCH_DATA_SUCCESSFULLY, 200, true, $data);
    }

    public function store(ContactCreateRequest $request)
    {
        try {
            $contact = $this->repository->saveContact($request);
            return $this->apiResponse('Contact created successfully', 201, true, ContactResource::make($contact));
        } catch (MarvelException $e) {
            throw new MarvelException(COULD_NOT_CREATE_THE_RESOURCE);
        }
    }

    public function show($id)
    {
        try {
            $contact = $this->repository->markAsRead($id);

            return $this->apiResponse(FETCH_DATA_SUCCESSFULLY, 200, true, ContactResource::make($contact));
        } catch (MarvelException $e) {
            throw new MarvelException(NOT_FOUND);
        }
    }

    public function sendReplay(ContactCreateReplayRequest $request, $id)
    {
        try {
            $contact = $this->repository->ReplayContact($request, $id);

            return $this->apiResponse('Replay sent successfully', 200, true, ContactResource::make($contact));
        } catch (MarvelException $e) {
            throw new MarvelException(NOT_FOUND);
        }
    }


    public function destroy($id)
    {
        try {
            $this->repository->findOrFail($id)->delete();

            return $this->apiResponse('Contact deleted successfully', 200, true);
        } catch (MarvelException $e) {
            throw new MarvelException(NOT_FOUND);
        }
    }


    public function deleteAll()
    {
        try {
            $this->repository->deleteAllContacts();

            return $this->apiResponse('All contacts deleted successfully', 200, true);
        } catch (MarvelException $e) {
            throw new MarvelException(NOT_FOUND);
        }
    }
    public function deleteAllReadContacts()
    {
        try {
            $this->repository->deleteAllReadContacts();

            return $this->apiResponse('All read contacts deleted successfully', 200, true);
        } catch (MarvelException $e) {
            throw new MarvelException(NOT_FOUND);
        }
    }
}