<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}"/>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.2.1/dist/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">

    <!-- Select 2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>

    <title>Company Job Post</title>
</head>

<body>

<div class="container">
    <div id="success-message-container" class="row d-none">
        <div class="col-md-12">
            <div class="alert alert-success" role="alert">
                {{-- <h4 class="alert-heading">Well done!</h4> --}}
                <p>Sinu tööpäring oli edukalt saadetud</p>
            </div>
        </div>
    </div>
    <div id="error-message-container" class="row d-none">
        <div class="col-md-12">
            <div class="alert alert-danger" role="alert">
                <h4 class="alert-heading">Errors!</h4>
                <div id="display-errors"></div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="jumbotron w-100 text-center">
            <h1 class="display-4">Saada päring tööpakkumise loomiseks</h1>
        </div>
    </div>

    <form id="jobPostForm" action="{{  route('company.job.store', ['token' => $token]) }}" method="post">
        <div class="row">
            <div class="col-sm-12 col-md-4">
                <div class="form-group">
                    <label for="offer-name">Nimetus <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="offer-name" name="offer_name"
                           aria-describedby="titleHelp" placeholder="Enter Job Offer Name">
                           <span class="input-error text-danger" id="offer_name_error"></span>
                </div>
            </div>
            <div class="col-xs-12 col-sm-4">
                <div class="form-group">
                    <label for="job-status">Staatus <span class="text-danger">*</span></label>
                    <select disabled id="job-status" class="form-control select2">
                        <option value="0" selected>Ootel</option>
                    </select>
                    <span class="input-error text-danger" id="status_error"></span>
                    <input type="hidden" name="status" value="3">
                </div>
            </div>
            <div class="col-xs-12 col-sm-4">
                <div class="form-group">
                    <label for="job-deadline">Soovitud tööle asumise aeg <span class="text-danger">*</span></label>
                    <textarea type="text" class="form-control" id="job-desired-start-time" name="desired_start_time"
                           aria-describedby="desired_start_time" placeholder="Sisesta Soovitud tööle asumise aeg"></textarea>
                           <span class="input-error text-danger" id="desired_start_time_error"></span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-4">
                <div class="form-group">
                    <label for="job-company">Ettevõte <span class="text-danger">*</span></label>
                    <input disabled type="text" class="form-control" id="job-company"
                           value="{{ $company_pr->company[0]->name }}" aria-describedby="companyHelp"
                           placeholder="Sisesta ettevõte">
                           <span class="input-error text-danger" id="company_pr_id_error"></span>
                    <input type="hidden" name="company_pr_id" value="{{ $company_pr->id }}">
                </div>
            </div>
            <div class="col-xs-12 col-sm-4">
                <div class="form-group">
                    <label for="job-candidate-required">Otsitavate inimeste hulk <span
                            class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="job-candidate-required" name="required_candidates"
                           aria-describedby="candidateRequiredHelp" placeholder="Enter Candidate Required">
                           <span class="input-error text-danger" id="required_candidates_error"></span>
                </div>
            </div>
            <div class="col-xs-12 col-sm-4">
                <div class="form-group">
                    <label for="job-benefits">Eelised</label>
                    <input type="text" class="form-control" id="job-benefits" name="benefits"
                           aria-describedby="titleHelp" placeholder="Sisesta eelised">
                           <span class="input-error text-danger" id="benefits_error"></span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-4">
                <div class="form-group">
                    <label for="job-description">Tööülesannete kirjeldus</label>
                    <input type="text" class="form-control" id="job-description" name="description"
                           aria-describedby="locationHelp" placeholder="Tööülesannete kirjeldus">
                           <span class="input-error text-danger" id="description_error"></span>
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <div class="form-group">
                    <label for="temporary-work-nature">Töö ajutise iseloomu põhjendus</label>
                    <select className="form-control" id="temporary-work-nature" name="temporary_work_nature"
                            required>
                        <option>Töömahu ajutine suurenemine</option>
                    </select>
                    <span class="input-error text-danger" id="temporary_work_nature_error"></span>
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <div class="form-group">
                    <label for="duration-type">Töö kestvuse tüüp<span class="text-danger">*</span></label>
                    <select id="duration-type" name="duration_type" class="form-control select2">
                        <option value="">Ei ole valitud</option>
                        <option value="1">püsiv töökoht</option>
                        <option selected value="2">ajutine töökoht</option>
                    </select>
                    <span class="input-error text-danger" id="duration_type_error"></span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-4">
                <div class="form-group">
                    <label for="job-location">Töö tegemise asukoht</label>
                    <input type="text" class="form-control" id="job-location" name="location" value="{{ $company_pr->companylocations[0]->location }}"
                           aria-describedby="locationHelp" placeholder="Sisesta aadress">
                           <span class="input-error text-danger" id="location_error"></span>
                </div>
            </div>
            <div class="col-xs-12 col-sm-4">
                <div class="form-group">
                    <label for="job-department">Osakond</label>
                    <input type="text" class="form-control" id="job-department" name="department"
                           aria-describedby="departmentHelp" placeholder="Sisesta osakond">
                           <span class="input-error text-danger" id="_departmenterror"></span>
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <div class="form-group">
                    <label for="job-contact-name">Kontaktisiku nimi <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="job-contact-name" name="contact_name"
                           value="{{ $company_pr->companyContacts[0]->name }}" aria-describedby="contactNameHelp" placeholder="Sisesta nimi">
                           <span class="input-error text-danger" id="contact_name_error"></span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-4">
                <div class="form-group">
                    <label for="job-contact-email">Kontaktisiku email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="job-contact-email" name="contact_email"
                    value="{{ $company_pr->companyContacts[0]->email }}" aria-describedby="emailHelp" placeholder="Sisesta email">
                    <span class="input-error text-danger" id="contact_email_error"></span>
                </div>
            </div>
            <div class="col-xs-12 col-sm-4">
                <div class="form-group">
                    <label for="job-contact-number">Kontaktisiku telefon <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="job-contact-number" name="contact_number"
                    value="{{ $company_pr->companyContacts[0]->phone }}" aria-describedby="contactNumberHelp" placeholder="Sisesta telefoninumber">
                    <span class="input-error text-danger" id="contact_number_error"></span>
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <div class="form-group">
                    <label for="job-training">Väljaõpe toimub ettevõttes kohapeal <span
                            class="text-danger">*</span></label>
                    <select id="job-training" name="training" class="form-control select2">
                        <option value="">Ei ole valitud</option>
                        {{-- <option value="1">Yes</option>--}}
                        <option selected value="1">Jah</option>
                        {{--<option value="0">No</option>--}}
                        <option value="0">Ei</option>
                    </select>
                    <span class="input-error text-danger" id="training_error"></span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-4">
                <div class="form-group">
                    <label for="job-observation">Töövaatluse võimalus<span class="text-danger">*</span></label>
                    <select id="job-observation" name="observation" class="form-control select2">
                        <option value="">Ei ole valitud</option>
                        {{--<option value="1">Yes</option>--}}
                        <option selected value="1">Jah</option>
                        {{--<option value="0">No</option>--}}
                        <option value="0">Ei</option>
                    </select>
                    <span class="input-error text-danger" id="observation_error"></span>
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <div class="form-group">
                    <label for="employment-type">Töö tüüp<span class="text-danger">*</span></label>
                    <select id="employment-type" name="employment_type" class="form-control select2">
                        <option value="">Ei ole valitud</option>
                        <option selected value="1">täistööaeg</option>
                        <option value="2">osaline tööaeg</option>
                        <option value="3">asendustöö</option>
                    </select>
                    <span class="input-error text-danger" id="employment_type_error"></span>
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <div class="form-group">
                    <label for="job-desired-language">Soovitud keeled</label>
                    <select id="job-desired-language" name="desired_language[]" class="form-control select2"
                            multiple>
                        <option value="4">eesti</option>
                        <option value="5">inglise</option>
                        <option value="6">vene</option>
                        <option value="">muu</option>
                    </select>
                    <span class="input-error text-danger" id="desired_language_error"></span>
                </div>
            </div>
            <div class="col-xs-12 col-sm-4">
                <div class="form-group">
                    <label for="job-working-language">Töökeel <span class="text-danger">*</span></label>
                    <select id="job-working-language" name="work_language[]" multiple class="form-control select2">
                        <option value="4">eesti</option>
                        <option value="5">inglise</option>
                        <option value="6">vene</option>
                    </select>
                    <span class="input-error text-danger" id="work_language_error"></span>
                </div>
            </div>
        </div>
        <div class="row d-none" id="job-comments-desired-language-div">
            <div class="col-sm-12 col-md-4">
                <div class="form-group">
                    <label for="job-comments-desired-language">Kommentaar <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="job-comments-desired-language"
                           name="desired_language_comment" aria-describedby="contactNameHelp"
                           placeholder="Enter Comments for Desired Language">
                           <span class="input-error text-danger" id="desired_language_comment_error"></span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-4">
                <div class="form-group">
                    <label for="job-salary">Töötasu <span class="text-danger">*</span></label>
                    <select id="job-salary" name="salary" class="form-control select2">
                        <option value="">Ei ole valitud</option>
                        <option value="Monthly">Kuus</option>
                        <option selected value="Hourly">Tunnis</option>
                    </select>
                    <span class="input-error text-danger" id="salary_error"></span>
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <div class="form-group">
                    <label for="job-salary-type">Töötasu <span class="text-danger">*</span></label>
                    <select id="job-salary-type" name="salary_type" class="form-control select2">
                        <option value="">Ei ole valitud</option>
                        <option selected value="Number">Number</option>
                        <option value="Range">Vahemik</option>
                    </select>
                    <span class="input-error text-danger" id="salary_type_error"></span>
                </div>
            </div>
            <div id="salary-1-container" class="col-sm-12 col-md-2">
                <div class="form-group">
                    <label for="job-salary-1"><span id="salary-1-label">Sisesta kogus</span> <span
                            class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">€</div>
                        </div>
                        <input type="number" class="form-control" id="salary-amount-1" name="salary_amount_1">
                        <span class="input-error text-danger" id="salary_amount_1_error"></span>
                    </div>
                </div>
            </div>
            <div id="salary-2-container" class="col-sm-12 col-md-2 d-none">
                <div class="form-group">
                    <div class="form-group">
                        <label id="salary-2-label" for="job-salary-1">Kuni <span
                                class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">€</div>
                            </div>
                            <input type="number" class="form-control" id="salary-amount-2" name="salary_amount_2">
                            <span class="input-error text-danger" id="salary_amount_2_error"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-4">
                <div class="form-group">
                    <label for="job-type">Renditöö vajadus <span class="text-danger">*</span></label>
                    <select id="job-type" name="job_type" class="form-control select2">
                        <option value="0">Pole valitud</option>
                        <option selected value="According to the contract">Vastavalt lepingule</option>
                        <option value="Seasonal Employee">Hooajatöötaja</option>
                    </select>
                    <span class="input-error text-danger" id="job_type_error"></span>
                </div>
            </div>
        </div>
        <div id="job-type-comment-container" class="row d-none">
            <div class="col-sm-12 col-md-4">
                <div class="form-group">
                    <label for="job-type-comment">Kommentaar <span class="text-danger">*</span></label>
                    <textarea name="job_type_comment" id="job-type-comment" class="form-control"></textarea>
                    <span class="input-error text-danger" id="job_type_comment_error"></span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-4">
                <div class="form-group">
                    <label for="job-transport">Töötajate transport<span class="text-danger">*</span></label>
                    <select id="job-transport" name="transport" class="form-control select2">
                        <option value="">Ei ole valitud</option>
                        <option value="Yes">Jah</option>
                        <option selected value="No">Ei</option>
                    </select>
                    <span class="input-error text-danger" id="transport_error"></span>
                </div>
            </div>
        </div>
        <div id="job-transport-comment-container" class="row d-none">
            <div class="col-sm-12 col-md-4">
                <div class="form-group">
                    <label for="job-transport-comment">Kommentaar<span class="text-danger">*</span></label>
                    <textarea name="transport_comment" id="job-transport-comment" class="form-control"></textarea>
                    <span class="input-error text-danger" id="transport_comment_error"></span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-4">
                <div class="form-group">
                    <label for="job-working-hours">Tööaeg <span class="text-danger">*</span></label>
                    <select id="job-working-hours" name="working_hours" class="form-control select2">
                        <option value="">Ei ole valitud</option>
                        <option value="Mon-Fri">E-R</option>
                        <option value="On Schedule">Graafiku alusel</option>
                    </select>
                    <span class="input-error text-danger" id="working_hours_error"></span>
                </div>
            </div>
        </div>
        <div id="job-working-hours-comment-container" class="row d-none">
            <div class="col-sm-12 col-md-4">
                <div class="form-group">
                    <label for="job-working-hours-comment">Kommentaar<span class="text-danger">*</span></label>
                    <textarea name="working_hours_comment" id="job-working-hours-comment"
                              class="form-control"></textarea>
                              <span class="input-error text-danger" id="working_hours_comment_error"></span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-4">
                <div class="form-group">
                    <label for="job-employee-clothes">Töötajate riided<span class="text-danger">*</span></label>
                    <select id="job-employee-clothes" name="clothes" class="form-control select2">
                        <option value="">Ei ole valitud</option>
                        <option value="Yes">Jah</option>
                        <option selected value="No">Ei</option>
                    </select>
                    <span class="input-error text-danger" id="clothes_error"></span>
                </div>
            </div>
        </div>
        <div id="job-employee-clothes-comment-container" class="row d-none">
            <div class="col-sm-12 col-md-4">
                <div class="form-group">
                    <label for="job-employee-clothes-comment">Kommentaar<span class="text-danger">*</span></label>
                    <textarea name="clothes_comment" id="job-employee-clothes-comment"
                              class="form-control"></textarea>
                              <span class="input-error text-danger" id="clothes_comment_error"></span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-4">
                <div class="form-group">
                    <label for="job-shifts">Töö mitmes vahetuses <span class="text-danger">*</span></label>
                    <select id="job-shifts" name="shifts" class="form-control select2">
                        <option value="">Ei ole valitud</option>
                        <option selected value="Yes">Jah</option>
                        <option value="No">Ei</option>
                    </select>
                    <span class="input-error text-danger" id="shifts_error"></span>
                </div>
            </div>
        </div>
        <div id="job-shifts-comment-container" class="row">
            <div id="job-shifts-comment-rows" class="col-sm-12 col-md-12">
                <div class="row">
                    <div class="col-sm-12 col-md-3">
                        <div class="form-group">
                            <label for="shift-start-time"><span class="shift_number">1</span>. vahetuse tööpäeva algus<span
                                    class="text-danger">*</span></label>
                            <input type="time" class="form-control"
                                   name="shifts_data[0][start_time]" aria-describedby="deadlineHelp"
                                   placeholder="Vali kellaaeg">
                                   <span class="input-error text-danger" id="shifts_data.0.start_time_error"></span>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-3">
                        <div class="form-group">
                            <label for="shift-end-time"><span class="shift_number">1</span>. vahetuse tööpäeva lõpp<span
                                    class="text-danger">*</span></label>
                            <input type="time" class="form-control"
                                   name="shifts_data[0][end_time]" aria-describedby="deadlineHelp"
                                   placeholder="Vali kellaaeg">
                                   <span class="input-error text-danger" id="shifts_data.0.end_time_error"></span>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-2 d-flex flex-column">
                        <button type="button" style="margin-top:2rem;" class="btn btn-danger remove-shift-row d-none">Kustuta</button>
                    </div>
                </div>
                {{-- <div class="row">
                    <div class="col-sm-12 col-md-3">
                        <div class="form-group">
                            <label for="job-shifts2-start-time"><span class="shift_number">2</span>. vahetuse tööpäeva algus<span
                                    class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="job-shifts2-start-time"
                                   name="shifts_data[1][start_time]" aria-describedby="deadlineHelp"
                                   placeholder="Select Time">
                                   <span class="input-error text-danger" id="shifts_data.1.start_time_error"></span>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-3">
                        <div class="form-group">
                            <label for="job-shifts2-end-time"><span class="shift_number">2</span>. vahetuse tööpäeva lõpp<span
                                    class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="job-shifts2-end-time"
                                   name="shifts_data[1][end_time]" aria-describedby="deadlineHelp"
                                   placeholder="Select Time">
                                   <span class="input-error text-danger" id="shifts_data.1.end_time_error"></span>
                        </div>
                    </div>
                </div> --}}
            </div>
            <div class="col-sm-12 col-md-12">
                <div class="row">
                    <div class="col-sm-12 col-md-3">
                        <button id="add-new-shift-row" type="button" class="btn btn-primary">Lisa vahetus</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-6">
                <div class="form-group">
                    <label for="job-recess">Lõuna ja puhkeaeg</label>
                    <input type="text" name="recess" id="job-recess" class="form-control">
                    <span class="input-error text-danger" id="_error"></span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-6">
                <div class="form-group">
                    <label for="job-requirements">Nõuded töötajate tervisele, teadmistele, oskustele, võimetele,
                        isikuomadustele</label>
                    <textarea name="requirements" id="job-requirements" class="form-control"></textarea>
                    <span class="input-error text-danger" id="requirements_error"></span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-6">
                <div class="form-group">
                    <label for="job-comments">Lisainfo</label>
                    <textarea name="comments" id="job-comments" class="form-control"></textarea>
                    <span class="input-error text-danger" id="_error"></span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-6">
                <div class="form-group">
                    <label for="job-files">Laadi üles ametijuhend</label><br/>
                    <input type="file" className="form-control" id="job-files" name="files[]" multiple/> <br/>
                    <span class="text-info">Lubatud formaadid: docx, xls, csv, zip, pdf, jpg, jpeg, png</span>
                    <span class="input-error text-danger" id="files_error"></span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-6">
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-6">
                <div class="form-group">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#video-modal">
                        Lisa video
                    </Button>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-6" id="videos-container">

            </div>
        </div>

        <div class="modal fade" id="video-modal" tabindex="-1" role="dialog" aria-labelledby="video-modal-Label" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Lisa video</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                    <h6 class="w-100 text-center"><strong>Kuidas soovid videot lisada?</strong></h6>
                    <div class="w-100 d-flex flex-row justify-content-center mt-3 mb-3">
                        <button data-dismiss="modal" data-toggle="modal" data-target="#video-modal-link" type="button" style="font-size: 14px;" class="mr-3 p-3 btn btn-primary">YouTube link</button>
                        <button data-dismiss="modal" data-toggle="modal" data-target="#video-modal-file" type="button" style="font-size: 14px;" class="p-3 btn btn-primary">Lae üles videofail</button>
                    </div>
                </div>
              </div>
            </div>
        </div>

        <div class="modal fade" id="video-modal-link" tabindex="-1" role="dialog" aria-labelledby="video-modal-link-Label" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">YouTube link</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                    <input id="input-video-link-value" placeholder="Enter YouTube Link" style="font-size: 14px;" type="text" class="form-control">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="add-video-link" >Salvesta</button>
                  </div>
              </div>
            </div>
        </div>

        <div class="modal fade" id="video-modal-file" tabindex="-1" role="dialog" aria-labelledby="video-modal-file-Label" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Lae üles failid</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                    <input id="input-video-file-value" style="font-size: 14px;" type="file" class="form-control">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="add-video-file">Salvesta</button>
                  </div>
              </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <button type="submit" class="btn btn-success" id="form-submit-button">
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    Kinnita päring
                </button>
                <button class="btn btn-danger">Tühista</button>
            </div>
        </div>
    </form>
    <div class="footer-copyright text-center py-3">© 2022 Copyright:
        <a href="#"> Hr System</a>
    </div>
</div>

<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.6/dist/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.2.1/dist/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>

<!-- Select 2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- malsup -->
<script src="https://malsup.github.io/jquery.form.js"></script>

<script>
    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function scrollToTop() {
            $(window).scrollTop(0);
        }

        $('button#add-video-link').click(function(){
            let link = $('input#input-video-link-value').val();

            let videos_container = $('#videos-container');
            let index = videos_container.children().length;

            let html_output = '<div class="row">';
                html_output += '<p>'+link+'</p>';
                html_output += '<div class="d-none">';
                html_output += '<input type="text" value="2" name="video['+index+'][type]">';
                html_output += '<input type="file" value="" name="video['+index+'][file]">';
                html_output += '<input type="text" value="'+link+'" name="video['+index+'][link]"> ';
                html_output += '</div>';
                html_output += '</div>';

            $(videos_container).append(html_output);
            $('input#input-video-link-value').val('');
            $('#video-modal-link').modal('hide');
        });
        $('button#add-video-file').click(function(){
            let file_name = $('input#input-video-file-value').val();
            // let file = $('input#input-video-file-value').prop('files');
            
            let videos_container = $('#videos-container');
            let index = videos_container.children().length;

            let file_cloned = $('input#input-video-file-value').clone().attr('name', 'video['+index+'][file]').removeAttr('id').removeAttr('style');
            console.log(file_cloned);

            let html_output = '<div class="row">';
                html_output += '<p>'+file_name.replace(/^.*[\\\/]/, '')+'</p>';
                html_output += '<div id="video_'+index+'_contianer" class="d-none">';
                html_output += '<input type="text" value="1" name="video['+index+'][type]">';
                // html_output += $(file_cloned).attr('name', "video['"+index+"'][file]");
                // html_output += '<input type="file" value="'+file_name+'" name="video['+index+'][file]">';
                html_output += '<input type="text" value="" name="video['+index+'][link]"> ';
                html_output += '</div>';
                html_output += '</div>';

            $(videos_container).append(html_output);
            // file_cloned.append('#video_'+index+'_contianer');

            $('#video_'+index+'_contianer').append(file_cloned);
            // let new_input_field = $('input[name="video['+index+'][file]"]');
            // new_input_field[0].setAttribute('files', file[0].files);
            // console.log($('input[name="video['+index+'][file]"]'));
            // new_input_field[0].files = file[0].files;
            // console.log($('input[name="video['+index+'][file]"]'));

            // console.log($('input[name="video['+index+'][file]"]').prop('files', file[0].files));
            // console.log($('input[name="video['+index+'][file]"]').prop('files'));

            $('input#input-video-file-value').val(null);
            $('#video-modal-file').modal('hide');
        });

        $('.select2').select2();

        $('#job-desired-language').change(function (e) {
            if ($(this).val().includes('')) {
                $('#job-comments-desired-language-div').removeClass('d-none')
            } else {
                $('#job-comments-desired-language-div').addClass('d-none')
            }
        })

        $('#job-salary-type').change(function (e) {
            if ($(this).val() == 'Number') {
                $('#salary-1-label').html('Enter Amount');
                $('#salary-1-container').removeClass('d-none');
                $('#salary-2-container').addClass('d-none');
            } else if ($(this).val() == 'Range') {
                $('#salary-1-label').html('Alates');
                $('#salary-1-container').removeClass('d-none');
                $('#salary-2-container').removeClass('d-none');
            } else {
                $('#salary-1-label').html('Enter Amount');
                $('#salary-1-container').addClass('d-none');
                $('#salary-2-container').addClass('d-none');
            }
        });

        $('#job-type').change(function () {
            if ($(this).val() == 'seasonal employee') {
                $('#job-type-comment-container').removeClass('d-none');
            } else {
                $('#job-type-comment-container').addClass('d-none');
            }
        });

        $('#job-transport').change(function () {
            if ($(this).val() == 'Yes') {
                $('#job-transport-comment-container').removeClass('d-none');
            } else {
                $('#job-transport-comment-container').addClass('d-none');
            }
        });

        $('#job-working-hours').change(function () {
            if ($(this).val() == 'On Schedule') {
                $('#job-working-hours-comment-container').removeClass('d-none');
            } else {
                $('#job-working-hours-comment-container').addClass('d-none');
            }
        });

        $('#job-employee-clothes').change(function () {
            if ($(this).val() == 'Yes') {
                $('#job-employee-clothes-comment-container').removeClass('d-none');
            } else {
                $('#job-employee-clothes-comment-container').addClass('d-none');
            }
        });

        function update_shift_index (){
            let total_records = $('div#job-shifts-comment-rows').children();
            if(total_records.length > 0){
                $(total_records).each(function (index){
                    $(this).find('span.shift_number').html(index+1);
                    $(this).find(':input[name^="shifts_data["]').each(function (){
                        $(this).attr("name", $(this).attr("name").replace(/\d+/, index));
                    });
                });
                
            }
        }

        function display_shift_delete_button (){
            let total_records = $('div#job-shifts-comment-rows').children();
            if(total_records.length > 1){
                $(total_records).each(function (index){
                    $(this).find('button.remove-shift-row').removeClass('d-none');
                });
            }else{
                $(total_records).each(function (index){
                    $(this).find('button.remove-shift-row').addClass('d-none');
                });
            }
        }

        $('#job-shifts').change(function () {
            if ($(this).val() == 'Yes') {
                $('#job-shifts-comment-container').removeClass('d-none');
                $(':input[name^="shifts_data["]').attr('disabled', false);
            } else {
                $('#job-shifts-comment-container').addClass('d-none');
                $(':input[name^="shifts_data["]').attr('disabled', true);
            }
        });

        $('#add-new-shift-row').click(function(){
            let cloned_shift_row = $('div#job-shifts-comment-rows').children('div.row:last-child').clone();
            $(cloned_shift_row).find(':input[name^="shifts_data["]').val('');
            $('div#job-shifts-comment-rows').append(cloned_shift_row);
            update_shift_index();
            display_shift_delete_button();
        });

        $('div#job-shifts-comment-rows').on('click', 'button.remove-shift-row', function (){
            let total_records = $('div#job-shifts-comment-rows').children();
            if(total_records.length > 1){
                $(this).closest('div.row').remove();
                update_shift_index();
            }
            display_shift_delete_button();
        });


        // post-submit callback
        function showResponse(responseText, statusText, xhr, $form) {
            if (responseText.hasOwnProperty('message')) {
                $('#success-message-container').removeClass('d-none');
                $(".btn-success, .btn-danger").hide();
            }
            scrollToTop();
        }

        var options = {
            // target: 'http://hra-dev.com/api/open/jobs',   // target element(s) to be updated with server response
            beforeSubmit: function (arr, $form, options) {
                $('.input-error').html('');
                $('#error-message-container').addClass('d-none');
                $('#jobPostForm,#form-submit-button').prop('disabled', true);
                $('span.spinner-border').removeClass('d-none');

                
            },
            success: showResponse,  // post-submit callback
            error: function (response) {
                let errors =  response.responseJSON.errors;
                for(let error in errors){
                    let output = '<ul>';
                        for(let i=0; i < errors[error].length; i++){
                            output += '<li>'+errors[error][i]+'</li>';
                        }
                    output += '</ul>';

                    $(`#${error}_error`).html(output)
                    console.log(error, output);
                }

                // $('#error-message-container').removeClass('d-none');
                $('#jobPostForm,#form-submit-button').prop('disabled', false);
                $('span.spinner-border').addClass('d-none');
                scrollToTop();
            },
            // clearForm: 1,
            // resetForm: 1
        };

        // bind 'myForm' and provide a simple callback function
        $('#jobPostForm').ajaxForm(options);

    });
</script>

</body>

</html>
