<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Repositories\Interfaces\UserRepositoryInterface as UserRepository;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use Auth;

class UserController extends Controller
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function index(Request $request)
    {
        $searchParams = $request->search;
        $perPage = config('paginate.perPage');
        $users = $this->userRepository->getAllAndSearch($perPage, $searchParams);
        if ($users->count() == 0 && $users->previousPageUrl()) {
            return redirect($users->previousPageUrl());
        } else {
            return view('admins.users.index', compact('users'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admins.users.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserCreateRequest $request)
    {
        try {
            $user = $this->userRepository->store($request);
            $data = ['name' => $request->name, 'email' => $request->email, 'password' => $request->password];
            $email = $request->email;
            Mail::send('mail/create_user_mail', $data, function ($message) use ($email) {
                $message->to($email)->subject('Welcome to Chatwork Forwarder Application');
            });
            return redirect()->route('users.edit', $user)
                ->with('messageSuccess', [
                    'status' => 'Create success',
                    'message' => 'This user successfully created',
                ]);
        } catch (QueryException $exception) {
            return redirect()->back()->with('messageFail', [
                'status' => 'Create success',
                'message' => 'Create failed. Something went wrong',
            ])->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return view('admins.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserUpdateRequest $request, $id)
    {
        try {
            $user = $this->userRepository->update($id, $request);

            return response()->json([
                'error' => false,
                'messageSuccess' => [
                    'status' => 'Update success',
                    'message' => 'This user successfully updated',
                ],
            ]);
        } catch (QueryException $exception) {
            return response()->json([
                'error' => true,
                'messageFail' => [
                    'status' => 'Update success',
                    'message' => 'Update failed. Something went wrong',
                ],
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, User $user)
    {
        $page = $request->page ? ['page' => $request->page] : null;
        try {
            if ($user->id == Auth::user()->id) {
                return redirect()->back()->with('messageFail', [
                    'status' => 'Delete failed',
                    'message' => 'Delete failed, Cannot delete myself',
                ]);
            } else {
                $this->userRepository->delete($user->id);
                return redirect()->route('users.index', $page)->with('messageSuccess', [
                    'status' => 'Delete success',
                    'message' => 'This user successfully deleted',
                ]);
            }
        } catch (Exception $exception) {
            return redirect()->back()->with('messageFail', [
                'status' => 'Delete failed',
                'message' => 'Delete failed. Something went wrong',
            ]);
        }
    }
}
