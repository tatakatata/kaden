<?php

require_once('lib/lime.php');
require_once('lib/carp.php');
require_once('lib/dynamixer.php');

global $dm;
global $t;

$t = new lime_test(11
                   , new lime_output_color());
$t->ok( $dm = new Dynamixer, 'constructer' );

$takashi = new Person('takashi');
$t->is_deeply( $takashi, $dm->get_instance('takashi'), 'get_instance()' );

ob_start();
$dm->call_assigned_method('profile');
$contents = ob_get_contents();
ob_end_clean();
$t->is_deeply($contents, "I am takashi.\n", 'call_assigned_method()');

$t->ok( $dm->set_instance(new Dog, 'dog'), 'set_instance() from outer' );
$t->ok( $dog = $dm->get_instance('dog'), 'get_instance()' );
$t->ok( $dm->assign_method(array($dog, 'bark'), 'profile'), 'assigned_method() from outer, first argument is array');

ob_start();
$dm->call_assigned_method('profile');
$contents = ob_get_contents();
ob_end_clean();
$t->is_deeply($contents, "Bow! Bow!\n" . "I am takashi.\n", 'call_assigned_method()');

$t->ok( $dog2 = $dm->load('Dog') );
$t->ok( method_exists($dog2, 'bark') );

class Person
{
    var $name;

    function __construct($name = 'nanashi'){
        global $t;
        global $dm;
        $this->name = $name;
        $t->ok($dm->set_instance($name), "set_instance() by component's constructer");
        $t->ok($dm->assign_method('profile'), "assign_method() by component's constructer");
    }

    function profile(){
        global $dm;
        echo "I am {$this->name}.\n";
        $dm->next_method();
    }
};

class Dog
{
    function bark(){
        global $dm;
        echo "Bow! Bow!\n";
        $dm->next_method();
    }
}
