<?php

class ApiUserController {
    public function show($id) {
        // Normally, you'd fetch this data from a database.
        $user = [
            'id' => $id,
            'name' => 'John Doe',
            'email' => 'johndoe@example.com'
        ];
        return $user;
    }
}
