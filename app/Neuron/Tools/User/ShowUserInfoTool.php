<?php
namespace App\Neuron\Tools\User;


class ShowUserInfoTool{

public function __invoke(){
   $user = request()->user();
    
    return $user;
}
  
}