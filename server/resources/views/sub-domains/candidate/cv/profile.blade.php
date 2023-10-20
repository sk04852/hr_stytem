<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.2.1/dist/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">

    <!-- Select 2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <title>Candidate CV</title>
</head>

<body>

    <div class="container">
        <div id="success-message-container" class="row d-none">
            <div class="col-md-12">
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading">Kandidaadi profiil!</h4>
                    <p>Oled edukalt oma profiili kinnitanud</p>
                    {{-- <hr>
                    <p class="mb-0">More Message you want to display</p> --}}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="jumbotron w-100 text-center">
                <h1 class="display-4">Kandidaadi profiil</h1>
            </div>
        </div>

        <form id="candidateProfileForm"
            action="{{ route('candidate.cv.profile.confirm', ['candidatecv_id' => $candidatecv_id, 'token' => $token]) }}"
            method="post">
            <section id="candidate-personal-information">
                <div>
                    <h1>Isiklik informatsioon</h1>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-4">
                        <div class="form-group">
                            <label for="first-name">Eesnimi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="first-name" name="first_name"
                                aria-describedby="titleHelp" value="<?= $candidatecv->first_name ?>" disabled
                                placeholder="Enter Eesnimi">
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <div class="form-group">
                            <label for="last-name">Perekonnanimi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="last-name" name="last_name"
                                aria-describedby="titleHelp" value="<?= $candidatecv->last_name ?>" disabled
                                placeholder="Enter Perekonnanimi">
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-4">
                        <div class="form-group">
                            <label for="gender">Sugu <span class="text-danger">*</span></label>
                            <select id="gender" disabled name="gender" class="form-control select2">
                                <option value="none">Ei ole valitud</option>
                                <option value="1"
                                    <?= $candidatecv->gender == 1 ? 'selected' : '' ?>>Mees
                                </option>
                                <option value="2"
                                    <?= $candidatecv->gender == 2 ? 'selected' : '' ?>>Naine
                                </option>
                            </select>
                            <div class="invalid-feedback">Invalid</div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-4">
                        <div class="form-group">
                            <label for="dob">Sünniaeg <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="dob" name="dob" disabled
                                value="<?= $candidatecv->dob ?>" aria-describedby="titleHelp">
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <div class="form-group">
                            <label for="personal-information">Isiklik informatsioon</label>
                            <input type="text" class="form-control" id="personal-information"
                                name="personal_information" value="<?= $candidatecv->personal_information ?>" disabled
                                aria-describedby="companyHelp" placeholder="Palun kirjuta siia">
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-4">
                        <div class="form-group">
                            <label for="personal-code">Isikukood</label>
                            <input type="number" class="form-control" id="personal-code" name="personal_code"
                                value="<?= $candidatecv->personal_code ?>" disabled
                                aria-describedby="candidateRequiredHelp" placeholder="XXXXXXXXXXX">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-4">
                        <div class="form-group">
                            <label for="phone">Telefon </label>
                            <input type="number" class="form-control" id="phone" name="phone"
                                aria-describedby="titleHelp" placeholder="+3725XXXXXXX"
                                value="<?= $candidatecv->phone ?>" disabled>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <div class="form-group">
                            <label for="email">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email"
                                value="<?= $candidatecv->email ?>" disabled aria-describedby="locationHelp"
                                placeholder="email@email.com">
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-4">
                        <div class="form-group">
                            <label for="location">Aadress</label>
                            <input type="text" class="form-control" id="location" name="location"
                                value="<?= $candidatecv->location ?>" disabled aria-describedby="departmentHelp"
                                placeholder="Tänav, maja, korter, linn, riik">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-4">
                        <div class="form-group">
                            <label for="marital-status">Perekonnaseis</label>
                            <input type="text" class="form-control" id="marital-status" name="marital_status"
                                value="<?= $candidatecv->marital_status ?>" disabled aria-describedby="creatorHelp"
                                placeholder="Perekonnaseis">
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <div class="form-group">
                            <label for="children-qty">Laste arv </label>
                            <input type="number" class="form-control" id="children-qty" name="children_qty"
                                value="<?= $candidatecv->children_qty ?>" disabled aria-describedby="contactNameHelp"
                                placeholder="1">
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-4">
                        <div class="form-group">
                            <label for="children-names">Laste nimed </label>
                            <input type="email" class="form-control" id="children-names" name="children_names"
                                value="<?= $candidatecv->children_names ?>" disabled aria-describedby="emailHelp"
                                placeholder="Nimi Perekonnanimi, Nimi2 Perekonnanimi2">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-4">
                        <div class="form-group">
                            <label for="age">Vanus </label>
                            <input type="number" class="form-control" id="age" name="age"
                                value="<?= $candidatecv->age ?>" disabled aria-describedby="contactNumberHelp"
                                placeholder="Vanus">
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <div class="form-group">
                            <label for="source">Allikas</label>
                            <input type="text" class="form-control" id="source" name="source"
                                value="<?= $candidatecv->source ?>" disabled aria-describedby="contactNumberHelp"
                                placeholder="Vanus">
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-4">
                        <div class="form-group">
                            <label for="mother-language">Emakeel </label>
                            <input type="text" class="form-control" id="mother-language" name="mother_language"
                                value="<?= $candidatecv->mother_language ?>" disabled
                                aria-describedby="contactNumberHelp">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-4">
                        <div class="form-group">
                            <label for="desired-job">Soovitud töökoht </label>
                            <input type="text" class="form-control" id="desired-job" name="desired_job"
                                value="<?= $candidatecv->desired_job ?>" disabled
                                aria-describedby="contactNumberHelp">
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <div class="form-group">
                            <label for="job-type">Soovitud töö liik </label>
                            <select id="job-type" name="job_type" class="form-control" disabled>
                                <option value="null">Ei ole valitud</option>
                                <option value="full_time"
                                    <?= $candidatecv->job_type == 'full_time' ? 'selected' : '' ?>>
                                    Täistööaeg
                                </option>
                                <option value="part_time"
                                    <?= $candidatecv->job_type == 'part_time' ? 'selected' : '' ?>>Osaline
                                    tööaeg
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <div class="form-group">
                            <label for="desired-job-location">Soovitud töö asukoht</label>
                            <input type="text" class="form-control" id="desired-job-location"
                                value="<?= $candidatecv->desired_job_location ?>" disabled name="desired_job_location"
                                aria-describedby="contactNameHelp">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-4">
                        <div class="form-group">
                            <label for="desired-salary">Soovitud töötasu</label>
                            <input type="text" class="form-control" id="desired-salary"
                                value="<?= $candidatecv->desired_salary ?>" disabled name="desired_salary"
                                aria-describedby="contactNameHelp">
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <div class="form-group">
                            <label for="action-id">Faas</label>
                            <select id="job-type" name="job_type" class="form-control select2" disabled>
                                <?php if ($actions->isNotEmpty()){ ?>
                                <?php foreach ($actions as $action){ ?>
                                <?php if ($action->id == $candidatecv->action_id){ ?>
                                <option value="<?= $action->id ?>" selected="selected"><?= $action->name ?></option>

                                <?php } else { ?>
                                <option value="<?= $action->id ?>"><?= $action->name ?></option>
                                <?php } ?>
                                <?php } ?>
                                <?php } ?>

                            </select>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <div class="form-group">
                            <label for="desired-salary">Staatus</label>
                            <select id="job-type" name="job_type" disabled class="form-control select2">
                                <option value="1" <?= $candidatecv->status == 1 ? 'selected' : '' ?>>
                                    Aktiivne
                                </option>
                                <option value="0" <?= $candidatecv->status == 0 ? 'selected' : '' ?>>
                                    Mitteaktiivne
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <div class="form-group">
                            <label for="desired-salary">Consent</label>
                            <input type="checkbox" checked disabled>
                        </div>
                    </div>
                </div>
                {{--            <div class="row"> --}}
                {{--                <div class="col-sm-12 col-md-4"> --}}
                {{--                    <div class="form-group"> --}}
                {{--                        <label for="file">Vali pilt: </label> --}}
                {{--                        <input type="file" class="form-control" id="file" --}}
                {{--                               name="file" aria-describedby="contactNameHelp"> --}}
                {{--                    </div> --}}
                {{--                </div> --}}
                {{--            </div> --}}
                <div class="row">
                    <div class="col-sm-12 col-md-8">
                        <label>Rahvus</label>
                        <div class="w-100 d-flex flex-row" id="nationalities-main-div">
                            <select disabled id="nationalities" multiple name="nationalities.[]"
                                class="form-control select2">
                                <?php if ($nationalities->isNotEmpty()){ ?>
                                <?php foreach ($nationalities as $nationality){ ?>
                                <?php if ($candidatecv->nationalities->isNotEmpty() && $candidatecv->nationalities->contains('id', $nationality->id)){ ?>
                                <option value="<?= $nationality->id ?>" selected="selected"><?= $nationality->name ?>
                                </option>

                                <?php } else { ?>
                                <option value="<?= $nationality->id ?>"><?= $nationality->name ?></option>
                                <?php } ?>
                                <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-4">
                        <label>Juhiluba</label>

                        <?php if ($candidatecv->driving_licenses->isNotEmpty()){ ?>
                        <?php foreach ($candidatecv->driving_licenses as $license) { ?>
                        <div class="w-100 d-flex flex-row mt-1">

                            <input disabled type="text" class="form-control" id="driving-licenses"
                                value="<?= $license->level ?>" name="driving_licenses.[].level"
                                aria-describedby="contactNameHelp">
                        </div>
                        <?php } ?>
                        <?php } else { ?>
                        <div class="w-100 d-flex flex-row">

                            <input disabled type="text" class="form-control" id="driving-licenses"
                                name="driving_licenses.[].level" aria-describedby="contactNameHelp">
                        </div>
                        <?php } ?>


                        {{--                        <div class="w-100 d-flex flex-row ml-1"> --}}
                        {{--                            <button type="button" class="btn btn-primary mr-1">-</button> --}}
                        {{--                            <button type="button" class="btn btn-primary">+</button> --}}
                        {{--                        </div> --}}

                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-12">
                        <label>Soovitajad</label>
                        <?php if ($candidatecv->recommendations->isNotEmpty()) { ?>
                        <?php foreach ($candidatecv->recommendations as $recommendation) { ?>
                        <div class="w-100 d-flex flex-row mt-1">
                            <textarea type="text" class="form-control" id="recommendations" name="recommendations.[]"
                                aria-describedby="contactNameHelp" disabled><?= $recommendation->recommendation ?></textarea>
                        </div>
                        <?php } ?>
                        <?php } else { ?>
                        <div class="w-100 d-flex flex-row">
                            <input type="text" class="form-control" id="recommendations"
                                name="recommendations.[]" aria-describedby="contactNameHelp" disabled>
                            {{--                        <div class="w-100 d-flex flex-row ml-1"> --}}
                            {{--                            <button type="button" class="btn btn-primary mr-1">-</button> --}}
                            {{--                            <button type="button" class="btn btn-primary">+</button> --}}
                            {{--                        </div> --}}
                        </div>
                        <?php } ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-8">
                        <label>Võtmesõnad</label>
                        <div class="w-100 d-flex flex-row">
                            <input type="text" class="form-control" id="keywords" disabled
                                value="<?= $candidatecv->keywords ?>" name="keywords.[]"
                                aria-describedby="contactNameHelp">
                            {{--                        <div class="w-100 d-flex flex-row ml-1"> --}}
                            {{--                            <button type="button" class="btn btn-primary mr-1">-</button> --}}
                            {{--                            <button type="button" class="btn btn-primary">+</button> --}}
                            {{--                        </div> --}}
                        </div>
                    </div>
                </div>
            </section>
            <section id="job-history-section" class="mt-5">
                <div>
                    <h1>Töö</h1>
                </div>
                <?php if ($candidatecv->jobHistory->isNotEmpty()){ ?>
                <?php foreach ($candidatecv->jobHistory as $key => $job){ ?>
                <div class="row">
                    <div class="col-sm-12 col-md-4">
                        <label>Ettevõtte nimetus</label>
                        <input type="text" class="form-control" id="jobs.[<?= $key ?>].company_name"
                            value="<?= $job->company_name ?>" disabled name="jobs.[<?= $key ?>].company_name"
                            aria-describedby="contactNameHelp">
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <label>Amet</label>
                        <input type="text" class="form-control" id="jobs.[<?= $key ?>].designation"
                            value="<?= $job->designation ?>" disabled name="jobs.[<?= $key ?>].designation"
                            aria-describedby="contactNameHelp">
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <label>Töökoha aadress</label>
                        <input type="text" class="form-control" id="jobs.[<?= $key ?>].work_place"
                            value="<?= $job->work_place ?>" disabled name="jobs.[<?= $key ?>].work_place"
                            aria-describedby="contactNameHelp">
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <label>Kirjeldus</label>
                        <input type="text" class="form-control" id="jobs.[<?= $key ?>].description"
                            value="<?= $job->description ?>" disabled name="jobs.[<?= $key ?>].description"
                            aria-describedby="contactNameHelp">
                    </div>
                    <div class="col-sm-12 col-md-4">
                        {{-- <label>Töö alguskuupäev</label> --}}
                        <div class="row">
                            <div class="col-sm-4 col-md-4">
                                <label>Aasta</label>
                                <input type="text" class="form-control" id="jobs.[<?= $key ?>].starting_year"
                                    value="<?= $job->starting_year ?>" disabled
                                    name="jobs.[<?= $key ?>].starting_year" aria-describedby="contactNameHelp">
                            </div>
                            <div class="col-sm-4 col-md-4">
                                <label>Kuu</label>
                                <input type="text" class="form-control" id="jobs.[<?= $key ?>].starting_month"
                                    value="<?= $job->starting_month ?>" disabled
                                    name="jobs.[<?= $key ?>].starting_month" aria-describedby="contactNameHelp">
                            </div>
                            <div class="col-sm-4 col-md-4">
                                <label>Päev</label>
                                <input type="text" class="form-control" id="jobs.[<?= $key ?>].starting_day"
                                    value="<?= $job->starting_day ?>" disabled name="jobs.[<?= $key ?>].starting_day"
                                    aria-describedby="contactNameHelp">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-4">
                        {{-- <label>Töö lõpp-kuupäev</label> --}}
                        <div class="row">
                            <div class="col-sm-4 col-md-4">
                                <label>Aasta</label>
                                <input type="text" class="form-control" id="jobs.[<?= $key ?>].ending_year"
                                    value="<?= $job->ending_year ?>" disabled name="jobs.[<?= $key ?>].ending_year"
                                    aria-describedby="contactNameHelp">
                            </div>
                            <div class="col-sm-4 col-md-4">
                                <label>Kuu</label>
                                <input type="text" class="form-control" id="jobs.[<?= $key ?>].ending_month"
                                    value="<?= $job->ending_month ?>" disabled name="jobs.[<?= $key ?>].ending_month"
                                    aria-describedby="contactNameHelp">
                            </div>
                            <div class="col-sm-4 col-md-4">
                                <label>Päev</label>
                                <input type="text" class="form-control" id="jobs.[<?= $key ?>].ending_day"
                                    value="<?= $job->ending_day ?>" disabled name="jobs.[<?= $key ?>].ending_day"
                                    aria-describedby="contactNameHelp">
                            </div>
                        </div>
                    </div>
                </div>
                <?php } ?>
                <?php } else { ?>

                <div class="row">
                    <div class="col-sm-12 col-md-4">
                        <label>Ettevõtte nimetus</label>
                        <input type="text" class="form-control" id="jobs.[].company_name" disabled
                            name="jobs.[].company_name" aria-describedby="contactNameHelp">
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <label>Amet</label>
                        <input type="text" class="form-control" id="jobs.[].designation" disabled
                            name="jobs.[].designation" aria-describedby="contactNameHelp">
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <label>Töökoha aadress</label>
                        <input type="text" class="form-control" id="jobs.[].work_place" disabled
                            name="jobs.[].work_place" aria-describedby="contactNameHelp">
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <label>Kirjeldus</label>
                        <input type="text" class="form-control" id="company-name" disabled
                            name="jobs.[].description" aria-describedby="contactNameHelp">
                    </div>
                    {{-- <div class="col-sm-12 col-md-4">
                        <label>Töö alguskuupäev</label>
                        <input type="date" class="form-control" id="company-name" disabled
                            name="jobs.[].starting_date" aria-describedby="contactNameHelp">
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <label>Töö lõppkuupäev</label>
                        <input type="date" class="form-control" id="company-name" disabled
                            name="jobs.[].ending_date" aria-describedby="contactNameHelp">
                    </div> --}}
                    <div class="col-sm-12 col-md-4">
                        <label>Töö alguskuupäev</label>
                        <div class="row">
                            <div class="col-sm-4 col-md-4">
                                <label>Aasta</label>
                                <input type="text" class="form-control" id="jobs.[1].starting_year"
                                    value="" disabled name="jobs.[1].starting_year"
                                    aria-describedby="contactNameHelp">
                            </div>
                            <div class="col-sm-4 col-md-4">
                                <label>Kuu</label>
                                <input type="text" class="form-control" id="jobs.[1].starting_month"
                                    value="" disabled name="jobs.[1].starting_month"
                                    aria-describedby="contactNameHelp">
                            </div>
                            <div class="col-sm-4 col-md-4">
                                <label>Päev</label>
                                <input type="text" class="form-control" id="jobs.[1].starting_day" value=""
                                    disabled name="jobs.[1].starting_day" aria-describedby="contactNameHelp">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <label>Töö lõpp-kuupäev</label>
                        <div class="row">
                            <div class="col-sm-4 col-md-4">
                                <label>Aasta</label>
                                <input type="text" class="form-control" id="jobs.[1].ending_year"
                                    value=" disabled
                                    name="jobs.[1].ending_year"
                                    aria-describedby="contactNameHelp">
                            </div>
                            <div class="col-sm-4 col-md-4">
                                <label>Kuu</label>
                                <input type="text" class="form-control" id="jobs.[1].ending_month" value=""
                                    disabled name="jobs.[1].ending_month" aria-describedby="contactNameHelp">
                            </div>
                            <div class="col-sm-4 col-md-4">
                                <label>Päev</label>
                                <input type="text" class="form-control" id="jobs.[1].ending_day" value=""
                                    disabled name="jobs.[1].ending_day" aria-describedby="contactNameHelp">
                            </div>
                        </div>
                    </div>
                </div>
                <?php } ?>
                {{--            <div class="mt-2"> --}}
                {{--                <button type="button" class="btn btn-primary">Lisa uus töökoht</button> --}}
                {{--            </div> --}}
            </section>
            <section id="education-section" class="mt-5">
                <div>
                    <h1>Haridus</h1>
                </div>
                <?php if ($candidatecv->education->isNotEmpty()){ ?>
                <?php foreach ($candidatecv->education as $key => $education) { ?>
                <div class="row">
                    <div class="col-sm-12 col-md-4">
                        <label>Teaduskraad</label>
                        <select disabled id="education_level" name="education.[<?= $key ?>].level_id"
                            class="form-control select2">
                            <?php if ($education_levels->isNotEmpty()){ ?>
                            <?php foreach ($education_levels as $education_level){ ?>
                            <?php if (!is_null($education->level_id) && $education->level_id == $education_level->id){ ?>
                            <option value="<?= $education_level->id ?>" selected="selected">
                                <?= $education_level->name ?>
                            </option>

                            <?php } else { ?>
                            <option value="<?= $education_level->id ?>" selected="selected">
                                <?= $education_level->name ?></option>
                            <?php } ?>
                            <?php } ?>
                            <?php } ?>
                        </select>

                    </div>
                    <div class="col-sm-12 col-md-4">
                        <label>Haridusasutus</label>
                        <input type="text" class="form-control" id="education.[<?= $key ?>].institute"
                            value="<?= $education->institute ?>" disabled name="education.[<?= $key ?>].institute"
                            aria-describedby="contactNameHelp">
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <label>Eriala</label>
                        <input type="text" class="form-control" id="education.[<?= $key ?>].speciality"
                            value="<?= $education->speciality ?>" disabled name="education.[<?= $key ?>].speciality"
                            aria-describedby="contactNameHelp">
                    </div>
                    <div class="col-sm-12 col-md-4">
                        {{-- <label>Sisseastumise kuupäev</label> --}}
                        <div class="row">
                            <div class="col-sm-4 col-md-4">
                                <label>Aasta</label>
                                <input type="text" class="form-control" id="education.[<?= $key ?>].starting_year"
                                    value="<?= $job->starting_year ?>" disabled
                                    name="education.[<?= $key ?>].starting_year" aria-describedby="contactNameHelp">
                            </div>
                            <div class="col-sm-4 col-md-4">
                                <label>Kuu</label>
                                <input type="text" class="form-control"
                                    id="education.[<?= $key ?>].starting_month" value="<?= $job->starting_month ?>"
                                    disabled name="education.[<?= $key ?>].starting_month"
                                    aria-describedby="contactNameHelp">
                            </div>
                            <div class="col-sm-4 col-md-4">
                                <label>Päev</label>
                                <input type="text" class="form-control" id="education.[<?= $key ?>].starting_day"
                                    value="<?= $job->starting_day ?>" disabled
                                    name="education.[<?= $key ?>].starting_day" aria-describedby="contactNameHelp">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-4">
                        {{-- <label>Lõpetamise kuupäev</label> --}}
                        <div class="row">
                            <div class="col-sm-4 col-md-4">
                                <label>Aasta</label>
                                <input type="text" class="form-control" id="education.[<?= $key ?>].ending_year"
                                    value="<?= $job->ending_year ?>" disabled
                                    name="education.[<?= $key ?>].ending_year" aria-describedby="contactNameHelp">
                            </div>
                            <div class="col-sm-4 col-md-4">
                                <label>Kuu</label>
                                <input type="text" class="form-control" id="education.[<?= $key ?>].ending_month"
                                    value="<?= $job->ending_month ?>" disabled
                                    name="education.[<?= $key ?>].ending_month" aria-describedby="contactNameHelp">
                            </div>
                            <div class="col-sm-4 col-md-4">
                                <label>Päev</label>
                                <input type="text" class="form-control" id="education.[<?= $key ?>].ending_day"
                                    value="<?= $job->ending_day ?>" disabled name="education.[<?= $key ?>].ending_day"
                                    aria-describedby="contactNameHelp">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <label>Lisainfo</label>
                        <input type="text" class="form-control" id="education.[<?= $key ?>].additonal_information"
                            value="<?= $education->additonal_information ?>" disabled
                            name="education.[<?= $key ?>].additonal_information" aria-describedby="contactNameHelp">
                    </div>
                </div>
                <?php } ?>
                <?php } else { ?>

                <div class="row">
                    <div class="col-sm-12 col-md-4">
                        <label>Teaduskraad</label>
                        <select disabled id="education_level" name="education.[].level_id"
                            class="form-control select2">
                            <?php if ($education_levels->isNotEmpty()){ ?>
                            <?php foreach ($education_levels as $education_level){ ?>

                            <option value="<?= $education_level->id ?>" selected="selected">
                                <?= $education_level->name ?></option>

                            <?php } ?>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <label>Haridusasutus</label>
                        <input type="text" class="form-control" id="education.[].institute" disabled
                            name="education.[].institute" aria-describedby="contactNameHelp">
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <label>Eriala</label>
                        <input type="text" class="form-control" id="education.[].speciality" disabled
                            name="education.[].speciality" aria-describedby="contactNameHelp">
                    </div>
                    <div class="col-sm-12 col-md-4">
                        {{-- <label>Sisseastumise kuupäev</label> --}}
                        <div class="row">
                            <div class="col-sm-4 col-md-4">
                                <label>Aasta</label>
                                <input type="text" class="form-control" id="education.[1].starting_year"
                                    value="" disabled
                                    name="education.[1].starting_year" aria-describedby="contactNameHelp">
                            </div>
                            <div class="col-sm-4 col-md-4">
                                <label>Kuu</label>
                                <input type="text" class="form-control"
                                    id="education.[1].starting_month" value=""
                                    disabled name="education.[1].starting_month"
                                    aria-describedby="contactNameHelp">
                            </div>
                            <div class="col-sm-4 col-md-4">
                                <label>Päev</label>
                                <input type="text" class="form-control" id="education.[1].starting_day"
                                    value="" disabled
                                    name="education.[1].starting_day" aria-describedby="contactNameHelp">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-4">
                        {{-- <label>Lõpetamise kuupäev</label> --}}
                        <div class="row">
                            <div class="col-sm-4 col-md-4">
                                <label>Aasta</label>
                                <input type="text" class="form-control" id="education.[1].ending_year"
                                    value="" disabled
                                    name="education.[1].ending_year" aria-describedby="contactNameHelp">
                            </div>
                            <div class="col-sm-4 col-md-4">
                                <label>Kuu</label>
                                <input type="text" class="form-control" id="education.[1].ending_month"
                                    value="" disabled
                                    name="education.[1].ending_month" aria-describedby="contactNameHelp">
                            </div>
                            <div class="col-sm-4 col-md-4">
                                <label>Päev</label>
                                <input type="text" class="form-control" id="education.[1].ending_day"
                                    value="" disabled name="education.[1].ending_day"
                                    aria-describedby="contactNameHelp">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <label>Lisainfo</label>
                        <input type="text" class="form-control" id="education.[1].additonal_information"
                            value="" disabled
                            name="education.[1].additonal_information" aria-describedby="contactNameHelp">
                    </div>
                </div>
                <?php } ?>
                {{--            <div class="mt-2"> --}}
                {{--                <button type="button" class="btn btn-primary">Lisa uus töökoht</button> --}}
                {{--            </div> --}}
            </section>
            <section id="additional-courses-section" class="mt-5">
                <div>
                    <h1>Täiendkoolitus</h1>
                </div>
                <?php if ($candidatecv->additionalCourses->isNotEmpty()){ ?>
                <?php foreach ($candidatecv->additionalCourses as $key => $additional_course) { ?>
                <div class="row">
                    <div class="col-sm-12 col-md-4">
                        <label>Nimetus</label>
                        <input type="text" class="form-control" id="courses.[<?= $key ?>].title"
                            value="<?= $additional_course->title ?>" disabled name="courses.[<?= $key ?>].title"
                            aria-describedby="contactNameHelp">
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <label>Kirjeldus</label>
                        <input type="text" class="form-control" id="courses.[<?= $key ?>].description"
                            value="<?= $additional_course->description ?>" disabled
                            name="courses.[<?= $key ?>].description" aria-describedby="contactNameHelp">
                    </div>
                    <div class="col-sm-12 col-md-4">
                        {{-- <label>Sisseastumise kuupäev</label> --}}
                        <div class="row">
                            <div class="col-sm-4 col-md-4">
                                <label>Aasta</label>
                                <input type="text" class="form-control" id="courses.[<?= $key ?>].starting_year"
                                    value="<?= $additional_course->starting_year ?>" disabled
                                    name="courses.[<?= $key ?>].starting_year" aria-describedby="contactNameHelp">
                            </div>
                            <div class="col-sm-4 col-md-4">
                                <label>Kuu</label>
                                <input type="text" class="form-control"
                                    id="courses.[<?= $key ?>].starting_month" value="<?= $additional_course->starting_month ?>"
                                    disabled name="courses.[<?= $key ?>].starting_month"
                                    aria-describedby="contactNameHelp">
                            </div>
                            <div class="col-sm-4 col-md-4">
                                <label>Päev</label>
                                <input type="text" class="form-control" id="courses.[<?= $key ?>].starting_day"
                                    value="<?= $additional_course->starting_day ?>" disabled
                                    name="courses.[<?= $key ?>].starting_day" aria-describedby="contactNameHelp">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-4">
                        {{-- <label>Lõpetamise kuupäev</label> --}}
                        <div class="row">
                            <div class="col-sm-4 col-md-4">
                                <label>Aasta</label>
                                <input type="text" class="form-control" id="courses.[<?= $key ?>].ending_year"
                                    value="<?= $additional_course->ending_year ?>" disabled
                                    name="courses.[<?= $key ?>].ending_year" aria-describedby="contactNameHelp">
                            </div>
                            <div class="col-sm-4 col-md-4">
                                <label>Kuu</label>
                                <input type="text" class="form-control" id="courses.[<?= $key ?>].ending_month"
                                    value="<?= $additional_course->ending_month ?>" disabled
                                    name="courses.[<?= $key ?>].ending_month" aria-describedby="contactNameHelp">
                            </div>
                            <div class="col-sm-4 col-md-4">
                                <label>Päev</label>
                                <input type="text" class="form-control" id="courses.[<?= $key ?>].ending_day"
                                    value="<?= $additional_course->ending_day ?>" disabled name="courses.[<?= $key ?>].ending_day"
                                    aria-describedby="contactNameHelp">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <label>Kokku tunde</label>
                        <input type="number" class="form-control" id="courses.[<?= $key ?>].total_hours"
                            value="<?= $additional_course->total_hours ?>" disabled
                            name="courses.[<?= $key ?>].total_hours" aria-describedby="contactNameHelp">
                    </div>
                </div>
                <?php } ?>
                <?php } else { ?>

                <div class="row">
                    <div class="col-sm-12 col-md-4">
                        <label>Nimetus</label>
                        <input type="text" class="form-control" id="courses.[].title" disabled
                            name="courses.[].title" aria-describedby="contactNameHelp">
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <label>Kirjeldus</label>
                        <input type="text" class="form-control" id="courses.[].description" disabled
                            name="courses.[].description" aria-describedby="contactNameHelp">
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <label>Kirjeldus</label>
                        <input type="date" class="form-control" id="courses.[].date" disabled
                            name="courses.[].date" aria-describedby="contactNameHelp">
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <label>Kokku tunde</label>
                        <input type="number" class="form-control" id="courses.[].total_hours" disabled
                            name="courses.[].total_hours" aria-describedby="contactNameHelp">
                    </div>
                </div>
                <?php } ?>
                {{--            <div class="mt-2"> --}}
                {{--                <button type="button" class="btn btn-primary">Lisa uus töökoht</button> --}}
                {{--            </div> --}}
            </section>
            <section id="languages-section" class="mt-5">
                <div>
                    <h1>Keeled</h1>
                </div>
                <?php if ($candidatecv->languages->isNotEmpty()){ ?>
                <?php foreach ($candidatecv->languages as $key => $language) { ?>
                <div class="row">
                    <div class="col-sm-12 col-md-4">
                        <label>Nimetus</label>
                        <input type="text" class="form-control" id="language.[<?= $key ?>].name"
                            value="<?= $language->name ?>" disabled name="language.[<?= $key ?>].name"
                            aria-describedby="contactNameHelp">
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <label>Suhtlemine</label>
                        <input type="text" class="form-control" id="language.[<?= $key ?>].speaking"
                            value="<?= $language->pivot->speaking ?>" disabled name="language.[<?= $key ?>].speaking"
                            aria-describedby="contactNameHelp">
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <label>Lugemine/kirjutamine</label>
                        <input type="text" class="form-control" id="language.[<?= $key ?>].reading_writing"
                            value="<?= $language->pivot->reading_writing ?>" disabled name="language.[<?= $key ?>].reading_writing"
                            aria-describedby="contactNameHelp">
                    </div>
                </div>
                <?php } ?>
                <?php } else { ?>

                <div class="row">
                    <div class="col-sm-12 col-md-4">
                        <label>Nimetus</label>
                        <input type="text" class="form-control" id="language.[].name" disabled
                            name="language.[].name" aria-describedby="contactNameHelp">
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <label>Suhtlemine</label>
                        <input type="text" class="form-control" id="language.[].speaking" disabled
                            name="language.[].speaking" aria-describedby="contactNameHelp">
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <label>Lugemine/kirjutamine</label>
                        <input type="text" class="form-control" id="language.[].reading_writing" disabled
                            name="language.[].reading_writing" aria-describedby="contactNameHelp">
                    </div>
                </div>
                <?php } ?>
                {{--            <div class="mt-2"> --}}
                {{--                <button type="button" class="btn btn-primary">Lisa uus töökoht</button> --}}
                {{--            </div> --}}
            </section>
            {{--        <section id="files-section" class="mt-5"> --}}
            {{--            <div> --}}
            {{--                <h1>Manused</h1> --}}
            {{--            </div> --}}
            {{--            <div class="row"> --}}
            {{--                <div class="col-sm-12 col-md-4"> --}}
            {{--                    <label>Nimetus</label> --}}
            {{--                    <input type="file" class="form-control" id="company-name" --}}
            {{--                           name="jobs.[].company_name" aria-describedby="contactNameHelp"> --}}
            {{--                </div> --}}
            {{--            </div> --}}
            {{--            <div class="mt-2"> --}}
            {{--                <button type="button" class="btn btn-primary">Lisa uus töökoht</button> --}}
            {{--            </div> --}}
            {{--        </section> --}}
            <div class="row mt-4">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-success" id="form-submit-button">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        Confirm
                    </button>
                    <span class="font-weight-bold ml-2">Call 166166 if you have questions</span>
                </div>
            </div>
        </form>
        <div class="footer-copyright text-center py-3">© 2022 Copyright:
            <a href="#"> Hr System</a>
        </div>
    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.1.min.js"
        integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.6/dist/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.2.1/dist/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>

    <!-- Select 2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- malsup -->
    <script src="https://malsup.github.io/jquery.form.js"></script>

    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('.select2').select2();

            function scrollToTop() {
                $(window).scrollTop(0);
            }


            // post-submit callback
            function showResponse(responseText, statusText, xhr, $form) {
                if (responseText.hasOwnProperty('message')) {
                    $('#success-message-container').removeClass('d-none');
                    $(".btn-success, .btn-danger").hide();
                }
                scrollToTop();
            }

            var options = {
                beforeSubmit: function(arr, $form, options) {
                    $('#candidateProfileForm,#form-submit-button').prop('disabled', true);
                    $('span.spinner-border').removeClass('d-none');
                },
                success: showResponse, // post-submit callback
                error: function() {
                    $('#candidateProfileForm,#form-submit-button').prop('disabled', false);
                    $('span.spinner-border').addClass('d-none');
                }
            };

            // bind 'myForm' and provide a simple callback function
            $('#candidateProfileForm').ajaxForm(options);

        });
    </script>
</body>

</html>
