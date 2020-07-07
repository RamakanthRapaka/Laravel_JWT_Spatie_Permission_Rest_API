<?php

/**
 */

namespace App\Repository\Transformers;

class UserDataTransformer extends Transformer {

    public function transform($user) {

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email
        ];
    }

}
