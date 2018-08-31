<?php

class MemberController extends \Phalcon\Mvc\Controller
{

    public function getAllMembers()
    {
        $members = Member::find();
        $this->response->setStatusCode(200, 'OK');
        $this->response->setJsonContent(
            [
                'status' => 'Uspesno',
                'members' => $members
            ]
        );
        return $this->response;
    }

    public function newMember()
    {
        $request = $this->request->getJsonRawBody();
        $response = $this->response;

        if (isset($request->f_new_company)) {
            if ($request->f_new_company === true) {
                $this->addNewMemberWithCompany();
            } else if ($request->f_new_company === false) {
                if (isset($request->company_id)) {
                    $this->addNewMember($request->company_id);
                } else {
                    $response->setStatusCode(409, 'Bad request');
                    $response->setJsonContent(
                        [
                            'status' => 'Greska',
                            'messages' => 'Nije prosledjeno polje company_id'
                        ]);
                }
            } else {
                $response->setStatusCode(409, 'Bad request');
                $response->setJsonContent(
                    [
                        'status' => 'Greska',
                        'messages' => 'Neispravno polje f_new_company'
                    ]);
            }
        } else {
            $response->setStatusCode(409, 'Bad request');
            $response->setJsonContent(
                [
                    'status' => 'Greska',
                    'messages' => 'Nije prosledjeno polje f_new_company'
                ]);
        }
        return $response;
    }

    private function addNewMember($companyId)
    {
        $request = $this->request->getJsonRawBody();
        $response = $this->response;

        $validationErrors = [];

        if (!isset($request->company_id) || empty(trim($request->company_id))) {
            $validationErrors[] = "Nije popunjeno polje company_id!";
        }
        if (!isset($request->first_name) || empty(trim($request->first_name))) {
            $validationErrors[] = "Nije popunjeno polje first_name!";
        } else {
            $firstName = $request->first_name;
        }
        if (!isset($request->last_name) || empty(trim($request->last_name))) {
            $validationErrors[] = "Nije popunjeno polje last_name!";
        } else {
            $lastName = $request->last_name;
        }
        if (!isset($request->date_of_birth) || empty(trim($request->date_of_birth))) {
            $validationErrors[] = "Nije popunjeno polje date_of_birth!";
        } else {
            $parsedDate = date_create($request->date_of_birth);
            if ($parsedDate === false) {
                $validationErrors[] = "Polje date_of_birth nije validan datum!";
            } else {
                $dateOfBirth = $parsedDate->format('Y-m-d');
            }
        }
        if (!isset($request->class) || empty(trim($request->class))) {
            $validationErrors[] = "Nije popunjeno polje class!";
        } else if (!ctype_digit($request->class)) {
            $validationErrors[] = "Polje class mora biti broj!";
        } else {
            $class = $request->class;
        }

        if (sizeof($validationErrors) > 0) {
            $response->setStatusCode(409, 'Bad request');
            $response->setJsonContent([
                "status" => "Greska",
                "messages" => $validationErrors
            ]);
        } else {
            $company = Company::findFirst(
                [
                    "company_id = :id:",
                    "bind" => [
                        "id" => $companyId
                    ]
                ]
            );
            if ($company) {
                $member = new Member();
                $member->first_name = $firstName;
                $member->last_name = $lastName;
                $member->date_of_birth = $dateOfBirth;
                $member->class = $class;
                $member->company = $company->company_id;

                if ($member->save()) {
                    $response->setStatusCode('201', 'Created');
                    $response->setJsonContent(['status' => 'Uspesno', 'message' => 'Uspesno sacuvan novi clan!']);
                } else {
                    $response->setStatusCode('500', 'Unexpected error');
                    $response->setJsonContent(['status' => 'Greska',
                        'message' => 'Došlo je do greške pri čuvanju člana u bazu!']);
                }
            } else {
                $response->setStatusCode(409, 'Bad request');
                $response->setJsonContent([
                    "status" => "Greska",
                    "messages" => "Ne postoji kompanija sa zadatim poljem company_id!"
                ]);
            }
        }
    }

    public function deleteMember($id)
    {
        $response = $this->response;
        if(!ctype_digit($id)) {
            $response->setStatusCode(409, 'Bad request');
            $response->setJsonContent(
                ["status" => "Greska",
                    "message" => "Neispravan id!"
                ]);
        } else {
            $member = Member::findFirst(
                [
                    "member_id = :id:",
                    "bind" => [
                        "id" => $id
                    ]
                ]
            );
            if($member) {
                if($member->delete()) {
                    $response->setStatusCode(200, 'OK');
                    $response->setJsonContent(
                        [
                            "status" => "Uspesno",
                            "message" => "Clan uspesno obrisan!"
                        ]);
                } else {
                    $response->setStatusCode(500, 'Unexpected error');
                    $response->setJsonContent(
                        [
                            "status" => "Greska",
                            "message" => "Doslo je do neocekivane greske pri brisanju clana!"
                        ]);
                }
            } else {
                $response->setStatusCode(404, 'Not found');
                $response->setJsonContent(
                    ["status" => "Greska",
                        "message"=> "Korisnik sa datim id-em ne postoji!"
                    ]);
            }
        }
        return $response;
    }

}

