<?php

require_once('lib/lime.php');
require_once('lib/kaden-carp.php');
require_once('lib/kaden-core-dynamixer.php');

$t = new lime_test(9
                   , new lime_output_color());

$t->ok( $dm = new Kaden_Core_Dynamixer(new DummyCx), 'constructer' );

$takashi = new Person($t, $dm, 'takashi');
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
$t->is_deeply($contents, "I am takashi.\n" . "Bow! Bow!\n", 'call_assigned_method()');


class DummyCx{};
class Person
{
    var $name;

    function __construct($t, $dm, $name = 'nanashi'){
        $this->name = $name;
        $t->ok($dm->set_instance($name), "set_instance() by component's constructer");
        $t->ok($dm->assign_method('profile'), "assign_method() by component's constructer");
    }

    function profile(){
        echo "I am {$this->name}.\n";
        return true;
    }
};

class Dog
{
    function bark(){
        echo "Bow! Bow!\n";
    }
}
