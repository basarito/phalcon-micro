<?php

use Phalcon\Mvc\Micro\Collection as MicroCollection;

/**
 * Local variables
 * @var \Phalcon\Mvc\Micro $app
 */

/**
 * Add your routes here
 */
$app->get('/', function () {
    echo $this['view']->render('index');
});

$member = new MicroCollection();
$member->setHandler(new MemberController());
$member->setPrefix('/member');

//ovde cemo dodavati rute za member-a

$member->get('/get-all', 'getAllMembers');
$member->post('/new', 'newMember');
$member->delete('/delete/{id}', 'deleteMember');

$app->get('/company/get-all', function() {
    $companies = Company::find();
    $this->response->setStatusCode(200, 'OK');
    $this->response->setJsonContent(['status' => "Uspesno", 'companies' => $companies]);
    return $this->response;
});

$app->mount($member);

/**
 * Not found handler
 */
$app->notFound(function () use($app) {
    $app->response->setStatusCode(404, "Not Found")->sendHeaders();
    echo $app['view']->render('404');
});
