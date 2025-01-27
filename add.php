<?php
require_once 'php/core/init.php';
$user = new User();
$override = new OverideData();
$email = new Email();
$random = new Random();
$validate = new validate();
$successMessage = null;
$pageError = null;
$errorMessage = null;
if ($user->isLoggedIn()) {
    if (Input::exists('post')) {
        if (Input::get('add_user')) {
            $validate = new validate();
            $validate = $validate->check($_POST, array(
                'firstname' => array(
                    'required' => true,
                ),
                'lastname' => array(
                    'required' => true,
                ),
                'position' => array(
                    'required' => true,
                ),
                'username' => array(
                    'required' => true,
                    'unique' => 'user'
                ),
                'phone_number' => array(
                    'required' => true,
                    'unique' => 'user'
                ),
                'email_address' => array(
                    'unique' => 'user'
                ),
            ));
            if ($validate->passed()) {
                $salt = $random->get_rand_alphanumeric(32);
                $password = '12345678';
                switch (Input::get('position')) {
                    case 1:
                        $accessLevel = 1;
                        break;
                    case 2:
                        $accessLevel = 2;
                        break;
                    case 3:
                        $accessLevel = 3;
                        break;
                }
                try {
                    $user->createRecord('user', array(
                        'firstname' => Input::get('firstname'),
                        'lastname' => Input::get('lastname'),
                        'username' => Input::get('username'),
                        'position' => Input::get('position'),
                        'phone_number' => Input::get('phone_number'),
                        'password' => Hash::make($password, $salt),
                        'salt' => $salt,
                        'create_on' => date('Y-m-d'),
                        'last_login' => '',
                        'status' => 1,
                        'power' => 0,
                        'email_address' => Input::get('email_address'),
                        'accessLevel' => $accessLevel,
                        'user_id' => $user->data()->id,
                        'count' => 0,
                        'pswd' => 0,
                    ));
                    $successMessage = 'Account Created Successful';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_position')) {
            $validate = $validate->check($_POST, array(
                'name' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $user->createRecord('position', array(
                        'name' => Input::get('name'),
                    ));
                    $successMessage = 'Position Successful Added';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_study')) {
            $validate = $validate->check($_POST, array(
                'name' => array(
                    'required' => true,
                ),
                'pi' => array(
                    'required' => true,
                ),
                'coordinator' => array(
                    'required' => true,
                ),
                'doctor' => array(
                    'required' => true,
                ),
                'start_date' => array(
                    'required' => true,
                ),
                'end_date' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $user->createRecord('study', array(
                        'name' => Input::get('name'),
                        'pi_id' => Input::get('pi'),
                        'co_id' => Input::get('coordinator'),
                        'do_id' => Input::get('doctor'),
                        'start_date' => Input::get('start_date'),
                        'end_date' => Input::get('end_date'),
                        'details' => Input::get('details'),
                        'date_created' => date('Y-m-d'),
                        'status' => 1,
                        'staff_id' => $user->data()->id,
                    ));
                    $successMessage = 'Study Successful Added';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_file')) {
            $validate = $validate->check($_POST, array(
                'name' => array(
                    'required' => true,
                ),
                'study' => array(
                    'required' => true,
                ),
                'type' => array(
                    'required' => true,
                )
            ));
            if ($validate->passed()) {
                try {
                    $user->createRecord('study_files', array(
                        'name' => Input::get('name'),
                        'study_id' => Input::get('study'),
                        'study_group' => Input::get('study_group'),
                        'screening_group' => Input::get('screening_group'),
                        'details' => Input::get('details'),
                        'created_date' => date('Y-m-d'),
                        'status' => 0,
                        'staff_id' => $user->data()->id,
                        'type' => Input::get('type')
                    ));
                    $successMessage = 'Study Files Successful Added';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_sensitization_file')) {
            $validate = $validate->check($_POST, array(
                'sensitization_name' => array(
                    'required' => true,
                )
            ));
            if ($validate->passed()) {
                try {
                    $user->createRecord('sensitization_files', array(
                        'name' => Input::get('sensitization_name'),
                        'status' => 0,
                        'staff_id' => $user->data()->id,
                    ));
                    $successMessage = 'Sensitization Files Successful Added';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('assign_file')) {
            $validate = $validate->check($_POST, array(
                'name' => array(
                    'required' => true,
                ),
                'staff' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $user->createRecord('study_files_rec', array(
                        'file_id' => Input::get('name'),
                        'details' => Input::get('details'),
                        'created_on' => date('Y-m-d'),
                        'staff_id' => Input::get('staff'),
                        'admin_id' => $user->data()->id,
                    ));
                    $user->updateRecord('study_files', array('status' => 1), Input::get('name'));
                    $successMessage = 'Study Files Successful Assigned';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('return_file')) {
            $validate = $validate->check($_POST, array(
                'name' => array(
                    'required' => true,
                ),
                'staff' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $f_req = $override->getNews('file_request', 'file_id', Input::get('name'), 'status', 2)[0];
                    $user->updateRecord('file_request', array(
                        'return_on' => date('Y-m-d H:i:s'),
                        'return_staff' => Input::get('staff'),
                        'received_staff' => $user->data()->id,
                        'status' => 1,
                    ), $f_req['id']);
                    $user->updateRecord('study_files', array('status' => 0), Input::get('name'));
                    $successMessage = 'Study Files Successful Returned';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_client')) {
            $validate = new validate();
            $validate = $validate->check($_POST, array(
                'file_id' => array(
                    'required' => true,
                    'unique' => 'clients',
                ),
                'study_id' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $user->createRecord('clients', array(
                        'study_id' => Input::get('study_id'),
                        'file_id' => Input::get('file_id'),
                        'create_on' => date('Y-m-d'),
                        'status' => 1,
                        'staff_id' => $user->data()->id
                    ));

                    $successMessage = 'Client Added Successful';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_request')) {
            $validate = new validate();
            $validate = $validate->check($_POST, array(
                'study_id' => array(
                    'required' => true,
                ),
                'type' => array(
                    'required' => true,
                )
            ));
            if ($validate->passed()) {
                try {
                    if (Input::get('staff_id')) {
                        $staff_id = Input::get('staff_id');
                    } else {
                        $staff_id = 0;
                    }
                    foreach (Input::get('file_id') as $fid) {
                        if (!$override->get3('file_request', 'file_id', $fid, 'status', 0, 'staff_id', $user->data()->id)) {
                            if ($staff_id != 0) {
                                $staff = Input::get('staff_id');
                            } else {
                                $staff = $user->data()->id;
                            }
                            $user->createRecord('file_request', array(
                                'study_id' => Input::get('study_id'),
                                'file_id' => $fid,
                                'create_on' => date('Y-m-d H:i:s'),
                                'return_on' => '',
                                'approved_on' => '',
                                'status' => 0,
                                'requesting_staff_id' => $staff,
                                'staff_id' => $user->data()->id,
                                'type' => Input::get('type'),
                                'details' => Input::get('details')
                            ));

                            $successMessage = 'Request Sent Successful';
                        } else {
                            $errorMessage = 'You have already submitted this request, Please wait for its approval';
                        }
                    }
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('request_sensitization_files')) {
            $validate = new validate();
            $validate = $validate->check($_POST, array(
                'study_sensitizatin_id' => array(
                    'required' => true,
                    // 'unique' => "file_request"
                )
            ));
            if ($validate->passed()) {
                try {
                    if (Input::get('staff_id')) {
                        $staff_id = Input::get('staff_id');
                    } else {
                        $staff_id = 0;
                    }
                    foreach (Input::get('file_id_sensitization') as $fid) {
                        if (!$override->get3('file_request', 'file_id', $fid, 'status', 0, 'staff_id', $user->data()->id)) {
                            if ($staff_id != 0) {
                                $staff = Input::get('staff_id');
                            } else {
                                $staff = $user->data()->id;
                            }
                            $user->createRecord('file_request', array(
                                'study_id' => Input::get('study_sensitizatin_id'),
                                'file_id' => $fid,
                                'create_on' => date('Y-m-d H:i:s'),
                                'return_on' => '',
                                'approved_on' => '',
                                'status' => 0,
                                'requesting_staff_id' => $staff,
                                'staff_id' => $user->data()->id,
                                'type' => 2
                            ));

                            $successMessage = 'Request Sensitization Sent Successful';
                        } else {
                            $errorMessage = 'You have already submitted this request, Please wait for its approval';
                        }
                    }
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_group')) {
            $validate = new validate();
            $validate = $validate->check($_POST, array(
                'group_id' => array(
                    'required' => true,
                    // 'unique' => 'clients',
                ),
                'study_id' => array(
                    'required' => true,
                )
                // 'name' => array(
                //     'required' => true,
                // ),
            ));
            if ($validate->passed()) {
                try {
                    $user->createRecord('study_group', array(
                        'study_id' => Input::get('study_id'),
                        'name' => Input::get('group_id'),
                        // 'name' => Input::get('name'),
                        'create_on' => date('Y-m-d'),
                        'status' => 1,
                        'staff_id' => $user->data()->id
                    ));

                    $successMessage = 'Group Added Successful';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        } elseif (Input::get('add_screening_group')) {
            $validate = new validate();
            $validate = $validate->check($_POST, array(
                'group_id' => array(
                    'required' => true,
                    // 'unique' => 'clients',
                )
                // 'study_id' => array(
                //     'required' => true,
                // )
                // 'name' => array(
                //     'required' => true,
                // ),
            ));
            if ($validate->passed()) {
                try {
                    $user->createRecord('screening_group', array(
                        // 'study_id' => Input::get('study_id'),
                        'name' => Input::get('group_id'),
                        // 'name' => Input::get('name'),
                        'create_on' => date('Y-m-d'),
                        'status' => 1,
                        'staff_id' => $user->data()->id
                    ));

                    $successMessage = 'Group Added Successful';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        }elseif (Input::get('return_file1')) {
            $validate = $validate->check($_POST, array(
                // 'name' => array(
                //     'required' => true,
                // ),
                'staff' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $f_req = $override->getNews('file_request', 'id', Input::get('file_request_id'),'status', 2)[0];
                    $user->updateRecord('file_request', array(
                        'return_on' => date('Y-m-d H:i:s'),
                        'return_staff' => Input::get('staff'),
                        'received_staff' => $user->data()->id,
                        'approve_staff' => $user->data()->id,
                        'status' => 1,
                    ), Input::get('file_request_id'));
                    $user->updateRecord('study_files', array('status' => 0), $f_req['file_id']);
                    $successMessage = 'Study Files Successful Returned';
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        }
    }
} else {
    Redirect::to('index.php');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title> Add - FileTrack </title>
    <?php include "head.php"; ?>
</head>

<body>
    <div class="wrapper">

        <?php include 'topbar.php' ?>
        <?php include 'menu.php' ?>
        <div class="content">


            <div class="breadLine">

                <ul class="breadcrumb">
                    <li><a href="#">Simple Admin</a> <span class="divider">></span></li>
                    <li class="active">Add Info</li>
                </ul>
                <?php include 'pageInfo.php' ?>
            </div>

            <div class="workplace">
                <?php if ($errorMessage) { ?>
                    <div class="alert alert-danger">
                        <h4>Error!</h4>
                        <?= $errorMessage ?>
                    </div>
                <?php } elseif ($pageError) { ?>
                    <div class="alert alert-danger">
                        <h4>Error!</h4>
                        <?php foreach ($pageError as $error) {
                            echo $error . ' , ';
                        } ?>
                    </div>
                <?php } elseif ($successMessage) { ?>
                    <div class="alert alert-success">
                        <h4>Success!</h4>
                        <?= $successMessage ?>
                    </div>
                <?php } ?>
                <div class="row">
                    <?php if ($_GET['id'] == 1 && $user->data()->position == 1) { ?>
                        <div class="col-md-offset-1 col-md-8">
                            <div class="head clearfix">
                                <div class="isw-ok"></div>
                                <h1>Add User</h1>
                            </div>
                            <div class="block-fluid">
                                <form id="validation" method="post">

                                    <div class="row-form clearfix">
                                        <div class="col-md-3">First Name:</div>
                                        <div class="col-md-9">
                                            <input value="" class="validate[required]" type="text" name="firstname" id="firstname" />
                                        </div>
                                    </div>
                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Last Name:</div>
                                        <div class="col-md-9">
                                            <input value="" class="validate[required]" type="text" name="lastname" id="lastname" />
                                        </div>
                                    </div>
                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Username:</div>
                                        <div class="col-md-9">
                                            <input value="" class="validate[required]" type="text" name="username" id="username" />
                                        </div>
                                    </div>

                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Position</div>
                                        <div class="col-md-9">
                                            <select name="position" style="width: 100%;" required>
                                                <option value="">Select position</option>
                                                <?php foreach ($override->getData('position') as $position) { ?>
                                                    <option value="<?= $position['id'] ?>"><?= $position['name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Phone Number:</div>
                                        <div class="col-md-9"><input value="" class="" type="text" name="phone_number" id="phone" required /> <span>Example: 0700 000 111</span></div>
                                    </div>

                                    <div class="row-form clearfix">
                                        <div class="col-md-3">E-mail Address:</div>
                                        <div class="col-md-9"><input value="" class="validate[required,custom[email]]" type="text" name="email_address" id="email" /> <span>Example: someone@nowhere.com</span></div>
                                    </div>

                                    <div class="footer tar">
                                        <input type="submit" name="add_user" value="Submit" class="btn btn-default">
                                    </div>

                                </form>
                            </div>

                        </div>
                    <?php } elseif ($_GET['id'] == 2 && $user->data()->position == 1) { ?>
                        <div class="col-md-offset-1 col-md-8">
                            <div class="head clearfix">
                                <div class="isw-ok"></div>
                                <h1>Add Position</h1>
                            </div>
                            <div class="block-fluid">
                                <form id="validation" method="post">
                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Name:</div>
                                        <div class="col-md-9">
                                            <input value="" class="validate[required]" type="text" name="name" id="name" />
                                        </div>
                                    </div>

                                    <div class="footer tar">
                                        <input type="submit" name="add_position" value="Submit" class="btn btn-default">
                                    </div>

                                </form>
                            </div>

                        </div>
                    <?php } elseif ($_GET['id'] == 3 && $user->data()->position == 1) { ?>
                        <div class="col-md-offset-1 col-md-8">
                            <div class="head clearfix">
                                <div class="isw-ok"></div>
                                <h1>Add Study</h1>
                            </div>
                            <div class="block-fluid">
                                <form id="validation" method="post">
                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Name: </div>
                                        <div class="col-md-9">
                                            <input value="" class="validate[required]" type="text" name="name" id="name" required />
                                        </div>
                                    </div>
                                    <div class="row-form clearfix">
                                        <div class="col-md-3">PI</div>
                                        <div class="col-md-9">
                                            <select name="pi" style="width: 100%;" required>
                                                <option value="">Select staff</option>
                                                <?php foreach ($override->getData('user') as $staff) { ?>
                                                    <option value="<?= $staff['id'] ?>"><?= $staff['firstname'] . ' ' . $staff['lastname'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Coordinator</div>
                                        <div class="col-md-9">
                                            <select name="coordinator" style="width: 100%;" required>
                                                <option value="">Select staff</option>
                                                <?php foreach ($override->getData('user') as $staff) { ?>
                                                    <option value="<?= $staff['id'] ?>"><?= $staff['firstname'] . ' ' . $staff['lastname'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Doctor</div>
                                        <div class="col-md-9">
                                            <select name="doctor" style="width: 100%;" required>
                                                <option value="">Select staff</option>
                                                <?php foreach ($override->getData('user') as $staff) { ?>
                                                    <option value="<?= $staff['id'] ?>"><?= $staff['firstname'] . ' ' . $staff['lastname'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Start Date:</div>
                                        <div class="col-md-9"><input type="text" name="start_date" id="mask_date" required /> <span>Example: 04/10/2012</span></div>
                                    </div>

                                    <div class="row-form clearfix">
                                        <div class="col-md-3">End Date:</div>
                                        <div class="col-md-9"><input type="text" name="end_date" id="mask_date" required /> <span>Example: 04/10/2012</span></div>
                                    </div>

                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Study details:</div>
                                        <div class="col-md-9"><textarea name="details" rows="4" required></textarea></div>
                                    </div>

                                    <div class="footer tar">
                                        <input type="submit" name="add_study" value="Submit" class="btn btn-default">
                                    </div>

                                </form>
                            </div>

                        </div>
                    <?php } elseif ($_GET['id'] == 4 && $user->data()->position == 1) { ?>
                        <div class="col-md-offset-1 col-md-8">
                            <div class="head clearfix">
                                <div class="isw-ok"></div>
                                <h1>Add </h1>
                            </div>
                            <div class="block-fluid">
                                <form id="validation" method="post">

                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Type</div>
                                        <div class="col-md-9">
                                            <select name="type" style="width: 100%;" required>
                                                <option value="">Choose Type</option>
                                                <?php foreach ($override->getData('doc_cat') as $study) { ?>
                                                    <option value="<?= $study['id'] ?>"><?= $study['name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Name:</div>
                                        <div class="col-md-9">
                                            <input value="" class="validate[required]" type="text" name="name" id="name" />
                                        </div>
                                    </div>

                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Study</div>
                                        <div class="col-md-9">
                                            <select name="study" style="width: 100%;" required>
                                                <option value="">Select Study</option>
                                                <option value="99">N/A</option>
                                                <?php foreach ($override->getData('study') as $study) { ?>
                                                    <option value="<?= $study['id'] ?>"><?= $study['name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Screening Group</div>
                                        <div class="col-md-9">
                                            <select name="screeing_group" style="width: 100%;">
                                                <option value="">Select Group</option>
                                                <?php foreach ($override->getData('screening_group') as $study) { ?>
                                                    <option value="<?= $study['id'] ?>"><?= $study['name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Study Group</div>
                                        <div class="col-md-9">
                                            <select name="study_group" style="width: 100%;">
                                                <option value="">Select Group</option>
                                                <?php foreach ($override->getData('study_group') as $study) { ?>
                                                    <option value="<?= $study['id'] ?>"><?= $study['name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>


                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Description:</div>
                                        <div class="col-md-9"><textarea name="details" rows="4"></textarea></div>
                                    </div>

                                    <div class="footer tar">
                                        <input type="submit" name="add_file" value="Submit" class="btn btn-default">
                                    </div>

                                </form>
                            </div>

                        </div>
                    <?php } elseif ($_GET['id'] == 5 && $user->data()->position == 1) { ?>
                        <div class="col-md-offset-1 col-md-8">
                            <div class="head clearfix">
                                <div class="isw-ok"></div>
                                <h1>Assign </h1>
                            </div>
                            <div class="block-fluid">
                                <form id="validation" method="post">

                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Study File</div>
                                        <div class="col-md-9">
                                            <select name="name" style="width: 100%;" required>
                                                <option value="">Select File</option>
                                                <?php foreach ($override->get('study_files', 'status', 0) as $study) { ?>
                                                    <option value="<?= $study['id'] ?>"><?= $study['name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Staff</div>
                                        <div class="col-md-9">
                                            <select name="staff" style="width: 100%;" required>
                                                <option value="">Select staff</option>
                                                <?php foreach ($override->getData('user') as $staff) { ?>
                                                    <option value="<?= $staff['id'] ?>"><?= $staff['firstname'] . ' ' . $staff['lastname'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>


                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Description:</div>
                                        <div class="col-md-9"><textarea name="details" rows="4"></textarea></div>
                                    </div>

                                    <div class="footer tar">
                                        <input type="submit" name="assign_file" value="Submit" class="btn btn-default">
                                    </div>

                                </form>
                            </div>

                        </div>
                    <?php } elseif ($_GET['id'] == 6) { ?>
                        <div class="col-md-offset-1 col-md-8">
                            <div class="head clearfix">
                                <div class="isw-ok"></div>
                                <h1>Return </h1>
                            </div>
                            <div class="block-fluid">
                                <form id="validation" method="post">
                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Study File</div>
                                        <div class="col-md-9">
                                            <select name="name" style="width: 100%;" required>
                                                <option value="">Select File</option>
                                                <?php foreach ($override->get('study_files', 'status', 1) as $study) { ?>
                                                    <option value="<?= $study['id'] ?>"><?= $study['name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Staff</div>
                                        <div class="col-md-9">
                                            <select name="staff" style="width: 100%;" required>
                                                <option value="">Select staff</option>
                                                <?php foreach ($override->getData('user') as $staff) { ?>
                                                    <option value="<?= $staff['id'] ?>"><?= $staff['firstname'] . ' ' . $staff['lastname'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>


                                    <div class="footer tar">
                                        <input type="submit" name="return_file" value="Submit" class="btn btn-default">
                                    </div>

                                </form>
                            </div>

                        </div>
                    <?php } elseif ($_GET['id'] == 7) { ?>
                        <div class="col-md-offset-1 col-md-8">
                            <div class="head clearfix">
                                <div class="isw-ok"></div>
                                <h1>Add </h1>
                            </div>
                            <div class="block-fluid">
                                <form id="validation" method="post">

                                    <div class="row-form clearfix">
                                        <div class="col-md-3">File Name: </div>
                                        <div class="col-md-9">
                                            <input value="" class="validate[required]" type="text" name="file_id" id="file_id" required />
                                        </div>
                                    </div>

                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Study</div>
                                        <div class="col-md-9">
                                            <select name="study_id" style="width: 100%;" required>
                                                <option value="">Select Study</option>
                                                <?php foreach ($override->getData('study') as $study) { ?>
                                                    <option value="<?= $study['id'] ?>"><?= $study['name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>


                                    <div class="footer tar">
                                        <input type="submit" name="add_client" value="Submit" class="btn btn-default">
                                    </div>

                                </form>
                            </div>

                        </div>
                    <?php } elseif ($_GET['id'] == 8) { ?>
                        <div class="col-md-offset-1 col-md-8">
                            <div class="head clearfix">
                                <div class="isw-ok"></div>
                                <h1>Request </h1>
                            </div>
                            <div class="block-fluid">
                                <form id="validation" method="post">

                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Type</div>
                                        <div class="col-md-9">
                                            <select name="type" id="type" style="width: 100%;" required>
                                                <option value="">Choose Type</option>
                                                <?php foreach ($override->getData('doc_cat') as $study) { ?>
                                                    <option value="<?= $study['id'] ?>"><?= $study['name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Study</div>
                                        <div class="col-md-9">
                                            <select name="study_id" id="study_id" style="width: 100%;" required>
                                                <option value="">Select Study</option>
                                                <?php foreach ($override->getData('study') as $study) { ?>
                                                    <option value="<?= $study['id'] ?>"><?= $study['name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <span><img src="img/loaders/loader.gif" id="fl_wait" title="loader.gif" /></span>
                                    <div class="row-form clearfix">
                                        <div class="col-md-3">File ID</div>
                                        <div class="col-md-9">
                                            <select name="file_id[]" id="s2_2" style="width: 100%;" multiple="multiple" required>
                                                <option value="">Select File</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row-form clearfix">
                                        <div class="col-md-3">OPTION COMMENT(PLEASE WRITE ANY RELEVANT REASON IF THE ITEMS WILL BE OUT OUT OF DATA UNIT FOR MORE THAN TWO DAYS):</div>
                                        <div class="col-md-9"><textarea name="details" rows="4"></textarea></div>
                                    </div>

                                    <?php if ($user->data()->position == 1 || $user->data()->position == 1) { ?>
                                        <div class="row-form clearfix">
                                            <div class="col-md-3">Staff</div>
                                            <div class="col-md-9">
                                                <select name="staff_id" style="width: 100%;" required>
                                                    <option value="0">None</option>
                                                    <?php foreach ($override->getData('user') as $staff) { ?>
                                                        <option value="<?= $staff['id'] ?>"><?= $staff['firstname'] . ' ' . $staff['lastname'] ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <div class="footer tar">
                                        <input type="submit" name="add_request" value="Submit" class="btn btn-default">
                                    </div>

                                </form>
                            </div>

                        </div>

                    <?php } elseif ($_GET['id'] == 9) { ?>
                        <div class="col-md-offset-1 col-md-8">
                            <div class="head clearfix">
                                <div class="isw-ok"></div>
                                <h1>Add Study Group</h1>
                            </div>
                            <div class="block-fluid">
                                <form id="validation" method="post">
                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Study</div>
                                        <div class="col-md-9">
                                            <select name="study_id" id="study_id" style="width: 100%;" required>
                                                <option value="">Select Study</option>
                                                <?php foreach ($override->getData('study') as $study) { ?>
                                                    <option value="<?= $study['id'] ?>"><?= $study['name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Group Name: </div>
                                        <div class="col-md-9">
                                            <input value="" class="validate[required]" type="text" name="group_id" id="group_id" required />
                                        </div>
                                    </div>
                                    <?php if ($user->data()->position == 1 || $user->data()->position == 1) { ?>
                                        <div class="row-form clearfix">
                                            <div class="col-md-3">Staff</div>
                                            <div class="col-md-9">
                                                <select name="staff_id" style="width: 100%;" required>
                                                    <option value="0">None</option>
                                                    <?php foreach ($override->getData('user') as $staff) { ?>
                                                        <option value="<?= $staff['id'] ?>"><?= $staff['firstname'] . ' ' . $staff['lastname'] ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <div class="footer tar">
                                        <input type="submit" name="add_group" value="Submit" class="btn btn-default">
                                    </div>

                                </form>
                            </div>

                        </div>

                    <?php } elseif ($_GET['id'] == 10) { ?>
                        <div class="col-md-offset-1 col-md-8">
                            <div class="head clearfix">
                                <div class="isw-ok"></div>
                                <h1>Add Screening Group</h1>
                            </div>
                            <div class="block-fluid">
                                <form id="validation" method="post">
                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Screening Group Name: </div>
                                        <div class="col-md-9">
                                            <input value="" class="validate[required]" type="text" name="group_id" id="group_id" required />
                                        </div>
                                    </div>
                                    <?php if ($user->data()->position == 1 || $user->data()->position == 1) { ?>
                                        <div class="row-form clearfix">
                                            <div class="col-md-3">Staff</div>
                                            <div class="col-md-9">
                                                <select name="staff_id" style="width: 100%;" required>
                                                    <option value="0">None</option>
                                                    <?php foreach ($override->getData('user') as $staff) { ?>
                                                        <option value="<?= $staff['id'] ?>"><?= $staff['firstname'] . ' ' . $staff['lastname'] ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <div class="footer tar">
                                        <input type="submit" name="add_screening_group" value="Submit" class="btn btn-default">
                                    </div>

                                </form>
                            </div>

                        </div>

                    <?php } elseif ($_GET['id'] == 11) { ?>
                        <div class="col-md-offset-1 col-md-8">
                            <div class="head clearfix">
                                <div class="isw-ok"></div>
                                <h1>Request Sensitization File</h1>
                            </div>
                            <div class="block-fluid">
                                <form id="validation" method="post">
                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Study</div>
                                        <div class="col-md-9">
                                            <select name="study_sensitizatin_id" id="study_sensitizatin_id" style="width: 100%;" required>
                                                <option value="">Select Study</option>
                                                <?php foreach ($override->getData('study') as $study) { ?>
                                                    <option value="<?= $study['id'] ?>"><?= $study['name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <span><img src="img/loaders/loader.gif" id="fl_wait" title="loader.gif" /></span>
                                    <div class="row-form clearfix">
                                        <div class="col-md-3">File ID</div>
                                        <div class="col-md-9">
                                            <select name="file_id_sensitization[]" id="s_file_id_senstzn" style="width: 100%;" multiple="multiple" required>
                                                <option value="">Select Sensitization File</option>
                                            </select>
                                        </div>
                                    </div>
                                    <?php if ($user->data()->position == 1 || $user->data()->position == 1) { ?>
                                        <div class="row-form clearfix">
                                            <div class="col-md-3">Staff</div>
                                            <div class="col-md-9">
                                                <select name="staff_id" style="width: 100%;" required>
                                                    <option value="0">None</option>
                                                    <?php foreach ($override->getData('user') as $staff) { ?>
                                                        <option value="<?= $staff['id'] ?>"><?= $staff['firstname'] . ' ' . $staff['lastname'] ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <div class="footer tar">
                                        <input type="submit" name="request_sensitization_files" value="Submit" class="btn btn-default">
                                    </div>

                                </form>
                            </div>

                        </div>

                    <?php } elseif ($_GET['id'] == 12 && $user->data()->position == 1) { ?>
                        <div class="col-md-offset-1 col-md-8">
                            <div class="head clearfix">
                                <div class="isw-ok"></div>
                                <h1>Add Sensitization File</h1>
                            </div>
                            <div class="block-fluid">
                                <form id="validation" method="post">
                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Name:</div>
                                        <div class="col-md-9">
                                            <input value="" class="validate[required]" type="text" name="sensitization_name" id="sensitization_name" />
                                        </div>
                                    </div>

                                    <div class="footer tar">
                                        <input type="submit" name="add_sensitization_file" value="Submit" class="btn btn-default">
                                    </div>

                                </form>
                            </div>

                        </div>
                    <?php } elseif ($_GET['id'] == 13) { ?>
                        <div class="col-md-offset-1 col-md-8">
                            <div class="head clearfix">
                                <div class="isw-ok"></div>
                                <h1>Return </h1>
                            </div>
                            <div class="block-fluid">
                                <form id="validation" method="post">                                    
                                    <div class="row-form clearfix">
                                        <div class="col-md-3">Staff</div>
                                        <div class="col-md-9">
                                            <select name="staff" style="width: 100%;" required>
                                                <option value="">Select staff</option>
                                                <?php foreach ($override->getData('user') as $staff) { ?>
                                                    <option value="<?= $staff['id'] ?>"><?= $staff['firstname'] . ' ' . $staff['lastname'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="footer tar">
                                        <input type="hidden" name="file_request_id" value="<?= $_GET['fid'] ?>" class="btn btn-default">
                                        <input type="submit" name="return_file1" value="Submit" class="btn btn-default">
                                    </div>

                                </form>
                            </div>

                        </div>
                    <?php } ?>
                    <div class="dr"><span></span></div>
                </div>

            </div>
        </div>
    </div>


    <script>
        <?php if ($user->data()->pswd == 0) { ?>
            $(window).on('load', function() {
                $("#change_password_n").modal({
                    backdrop: 'static',
                    keyboard: false
                }, 'show');
            });
        <?php } ?>
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }

        $(document).ready(function() {
            $('#fl_wait').hide();
            $('#wait_ds').hide();
            $('#region').change(function() {
                var getUid = $(this).val();
                $('#wait_ds').show();
                $.ajax({
                    url: "process.php?cnt=region",
                    method: "GET",
                    data: {
                        getUid: getUid
                    },
                    success: function(data) {
                        $('#ds_data').html(data);
                        $('#wait_ds').hide();
                    }
                });
            });
            $('#wait_wd').hide();
            $('#ds_data').change(function() {
                $('#wait_wd').hide();
                var getUid = $(this).val();
                $.ajax({
                    url: "process.php?cnt=district",
                    method: "GET",
                    data: {
                        getUid: getUid
                    },
                    success: function(data) {
                        $('#wd_data').html(data);
                        $('#wait_wd').hide();
                    }
                });

            });

            $('#a_cc').change(function() {
                var getUid = $(this).val();
                $('#wait').show();
                $.ajax({
                    url: "process.php?cnt=payAc",
                    method: "GET",
                    data: {
                        getUid: getUid
                    },
                    success: function(data) {
                        $('#cus_acc').html(data);
                        $('#wait').hide();
                    }
                });

            });


            // $('#study_id').change(function() {
            //     var getUid = $(this).val();
            //     var type = $('#type').val();
            //     $('#fl_wait').show();
            //     $.ajax({
            //         url: "process.php?cnt=study",
            //         method: "GET",
            //         data: {
            //             getUid: getUid,
            //             type: type
            //         },
            //         success: function(data) {
            //             $('#s2_2').html(data);
            //             $('#fl_wait').hide();
            //         }
            //     });

            // });


            $('#study_id').change(function() {
                var getUid = $(this).val();
                var type = $('#type').val();
                $('#fl_wait').show();
                $.ajax({
                    url: "process.php?cnt=study",
                    method: "GET",
                    data: {
                        getUid: getUid,
                        type: type
                    },

                    success: function(data) {
                        console.log(data);
                        $('#s2_2').html(data);
                        $('#fl_wait').hide();
                    }
                });

            });

        });
    </script>
</body>

</html>