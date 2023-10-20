<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.2.1/dist/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    

    <!-- Select 2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <title>Company Job Post</title>
</head>

<body>

    <?php
    // dd($job_pr->shifts()->get());
    ?>
    <div class="container">
        <div id="success-message-container" class="row d-none">
            <div class="col-md-12">
                <div class="alert alert-success" role="alert">
                    {{-- <h4 class="alert-heading">Well done!</h4> --}}
                    <p>Tööpakkumine on edukalt kinnitatud</p>
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
                <h1 class="display-4">Kinnita tööpakkumine</h1>
            </div>
        </div>

        <form id="jobPostForm"
            action="{{ route('company.job.verified', ['token' => $token, 'job_pr_id' => $job_pr_id]) }}" method="post">
            <div class="row">
                <div class="col-sm-12 col-md-4">
                    <div class="form-group">
                        <label for="offer-name">Nimetus <span class="text-danger">*</span></label>
                        <input type="text" disabled class="form-control" id="offer-name" name="offer_name"
                            aria-describedby="titleHelp" placeholder="Enter Job Offer Name"
                            value="{{ $job_pr->jobs->first()->offer_name }}">
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
                        <label for="job-deadline">Tähtaeg</label>
                        <input type="date" class="form-control" id="job-deadline" name="deadline"
                            aria-describedby="deadline" placeholder="Tähtaeg" disabled value="{{ $job_pr->deadline }}">
                        <span class="input-error text-danger" id="deadline_error"></span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-4">
                    <div class="form-group">
                        <label for="job-company">Ettevõte <span class="text-danger">*</span></label>
                        <input disabled type="text" class="form-control" id="job-company"
                            value="{{ $job_pr->companyPr->company[0]->name }}" aria-describedby="companyHelp"
                            placeholder="Sisesta ettevõte">
                        <span class="input-error text-danger" id="job_pr_id_error"></span>
                        <input disabled type="hidden" name="job_pr_id" value="{{ $job_pr->companyPr->id }}">
                    </div>
                </div>
                <div class="col-xs-12 col-sm-4">
                    <div class="form-group">
                        <label for="job-candidate-required">Otsitavate inimeste hulk <span
                                class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="job-candidate-required"
                            name="required_candidates" aria-describedby="candidateRequiredHelp"
                            placeholder="Enter Candidate Required" disabled value="{{ $job_pr->required_candidates }}">
                        <span class="input-error text-danger" id="required_candidates_error"></span>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-4">
                    <div class="form-group">
                        <label for="job-benefits">Eelised</label>
                        <input type="text" class="form-control" id="job-benefits" name="benefits"
                            aria-describedby="titleHelp" placeholder="Sisesta eelised" disabled
                            value="{{ $job_pr->jobs->first()->benefits }}">
                        <span class="input-error text-danger" id="benefits_error"></span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-4">
                    <div class="form-group">
                        <label for="job-description">Tööülesannete kirjeldus <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="job-description" name="description"
                            aria-describedby="locationHelp" placeholder="Tööülesannete kirjeldus" disabled
                            value="{{ $job_pr->jobs->first()->description }}">
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
                        <select id="duration-type" name="duration_type" class="form-control select2" disabled>
                            <option value="">Ei ole valitud</option>
                            <option value="1" {{ $job_pr->duration_type == 1 ? 'selected' : '' }}>püsiv töökoht</option>
                            <option selected value="2" {{ $job_pr->duration_type == 2 ? 'selected' : '' }}>ajutine töökoht</option>
                        </select>
                        <span class="input-error text-danger" id="duration_type_error"></span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-4">
                    <div class="form-group">
                        <label for="job-location">Töö tegemise asukoht <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="job-location" name="location"
                            aria-describedby="locationHelp" placeholder="Sisesta aadress" disabled
                            value="{{ $job_pr->jobs->first()->location }}">
                        <span class="input-error text-danger" id="location_error"></span>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-4">
                    <div class="form-group">
                        <label for="job-department">Osakond</label>
                        <input type="text" class="form-control" id="job-department" name="department"
                            aria-describedby="departmentHelp" placeholder="Sisesta osakond" disabled
                            value="{{ $job_pr->jobs->first()->department }}">
                        <span class="input-error text-danger" id="department_error"></span>
                    </div>
                </div>
                <div class="col-sm-12 col-md-4">
                    <div class="form-group">
                        <label for="job-contact-name">Kontaktisiku nimi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="job-contact-name" name="contact_name"
                            disabled value="{{ $job_pr->contact_name }}" aria-describedby="contactNameHelp"
                            placeholder="Sisesta nimi">
                        <span class="input-error text-danger" id="contact_name_error"></span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-4">
                    <div class="form-group">
                        <label for="job-contact-email">Kontaktisiku email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="job-contact-email" name="contact_email"
                            disabled value="{{ $job_pr->contact_email }}" aria-describedby="emailHelp"
                            placeholder="Sisesta email">
                        <span class="input-error text-danger" id="contact_email_error"></span>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-4">
                    <div class="form-group">
                        <label for="job-contact-number">Kontaktisiku telefon <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="job-contact-number" name="contact_number"
                            disabled value="{{ $job_pr->contact_number }}" aria-describedby="contactNumberHelp"
                            placeholder="Sisesta telefoninumber">
                        <span class="input-error text-danger" id="contact_number_error"></span>
                    </div>
                </div>
                <div class="col-sm-12 col-md-4">
                    <div class="form-group">
                        <label for="job-training">Väljaõpe toimub ettevõttes kohapeal <span
                                class="text-danger">*</span></label>
                        <select disabled id="job-training" name="training" class="form-control select2">
                            <option selected value="">Ei ole valitud</option>
                            <option value="1" {{ $job_pr->training == 1 ? 'selected' : '' }}>Jah</option>
                            <option value="0" {{ $job_pr->training == 0 ? 'selected' : '' }}>Ei</option>
                        </select>
                        <span class="input-error text-danger" id="training_error"></span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-4">
                    <div class="form-group">
                        <label for="job-observation">Töövaatluse võimalus<span class="text-danger">*</span></label>
                        <select disabled id="job-observation" name="observation" class="form-control select2">
                            <option selected value="">Ei ole valitud</option>
                            <option value="1" {{ $job_pr->observation == 1 ? 'selected' : '' }}>Jah</option>
                            <option value="0" {{ $job_pr->observation == 0 ? 'selected' : '' }}>Ei</option>
                        </select>
                        <span class="input-error text-danger" id="observation_error"></span>
                    </div>
                </div>
                <div class="col-sm-12 col-md-4">
                    <div class="form-group">
                        <label for="employment-type">Töö tüüp<span class="text-danger">*</span></label>
                        <select id="employment-type" name="employment_type" class="form-control select2" disabled>
                            <option value="">Ei ole valitud</option>
                            <option value="1" {{ $job_pr->employment_type == 1 ? 'selected' : '' }}>täistööaeg</option>
                            <option value="2" {{ $job_pr->employment_type == 2 ? 'selected' : '' }}>osaline tööaeg</option>
                            <option value="3" {{ $job_pr->employment_type == 3 ? 'selected' : '' }}>asendustöö</option>
                        </select>
                        <span class="input-error text-danger" id="employment_type_error"></span>
                    </div>
                </div>
                <div class="col-sm-12 col-md-4">
                    <div class="form-group">
                        <label for="job-desired-language">Soovitud keeled <span class="text-danger">*</span></label>
                        <select disabled id="job-desired-language" name="desired_language[]"
                            class="form-control select2" multiple>
                            <option value="4"
                                {{ $job_pr->desiredlanguages->contains('id', 4) ? 'selected' : '' }}>eesti</option>
                            <option value="5"
                                {{ $job_pr->desiredlanguages->contains('id', 5) ? 'selected' : '' }}>inglise</option>
                            <option value="6"
                                {{ $job_pr->desiredlanguages->contains('id', 6) ? 'selected' : '' }}>vene</option>
                            <option value="" {{ is_null($job_pr->desired_language_comment) ? '' : 'selected' }}>
                                muu</option>
                        </select>
                        <span class="input-error text-danger" id="desired_language_error"></span>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-4">
                    <div class="form-group">
                        <label for="job-working-language">Töökeel <span class="text-danger">*</span></label>
                        <select disabled id="job-working-language" name="work_language[]" multiple
                            class="form-control select2">
                            <option value="4"
                                {{ $job_pr->workinglanguages->contains('id', 4) ? 'selected' : '' }}>eesti</option>
                            <option value="5"
                                {{ $job_pr->workinglanguages->contains('id', 5) ? 'selected' : '' }}>inglise</option>
                            <option value="6"
                                {{ $job_pr->workinglanguages->contains('id', 6) ? 'selected' : '' }}>vene</option>
                        </select>
                        <span class="input-error text-danger" id="work_language_error"></span>
                    </div>
                </div>
            </div>
            <div class="row {{ is_null($job_pr->desired_language_comment) ? 'd-none' : '' }}"
                id="job-comments-desired-language-div">
                <div class="col-sm-12 col-md-4">
                    <div class="form-group">
                        <label for="job-comments-desired-language">Kommentaar <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="job-comments-desired-language"
                            name="desired_language_comment" aria-describedby="contactNameHelp"
                            placeholder="Enter Comments for Desired Language" disabled
                            value={{ $job_pr->desired_language_comment }}>
                        <span class="input-error text-danger" id="desired_language_comment_error"></span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-4">
                    <div class="form-group">
                        <label for="job-salary">Töötasu <span class="text-danger">*</span></label>
                        <select disabled id="job-salary" name="salary" class="form-control select2">
                            <option value="">Ei ole valitud</option>
                            <option value="Monthly" {{ $job_pr->salary == 'Monthly' ? 'selected' : '' }}>Kuus
                            </option>
                            <option value="Hourly" {{ $job_pr->salary == 'Hourly' ? 'selected' : '' }}>Tunnis
                            </option>
                        </select>
                        <span class="input-error text-danger" id="salary_error"></span>
                    </div>
                </div>
                <div class="col-sm-12 col-md-4">
                    <div class="form-group">
                        <label for="job-salary-type">Töötasu <span class="text-danger">*</span></label>
                        <select disabled id="job-salary-type" name="salary_type" class="form-control select2">
                            <option value="">Ei ole valitud</option>
                            <option value="Number" {{ $job_pr->salary_type == 'Number' ? 'selected' : '' }}>Number
                            </option>
                            <option value="Range" {{ $job_pr->salary_type == 'Range' ? 'selected' : '' }}>Vahemik
                            </option>
                        </select>
                        <span class="input-error text-danger" id="salary_type_error"></span>
                    </div>
                </div>
                <div id="salary-1-container"
                    class="col-sm-12 col-md-2 {{ is_null($job_pr->salary_amount_1) ? 'd-none' : '' }}">
                    <div class="form-group">
                        <label for="job-salary-1"><span id="salary-1-label">Sisesta kogus</span> <span
                                class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">€</div>
                            </div>
                            <input type="number" class="form-control" id="salary-amount-1" name="salary_amount_1"
                                disabled value={{ $job_pr->salary_amount_1 }}>
                            <span class="input-error text-danger" id="salary_amount_1_error"></span>
                        </div>
                    </div>
                </div>
                <div id="salary-2-container"
                    class="col-sm-12 col-md-2 {{ is_null($job_pr->salary_amount_2) ? 'd-none' : '' }}">
                    <div class="form-group">
                        <div class="form-group">
                            <label id="salary-2-label" for="job-salary-1">Kuni <span
                                    class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">€</div>
                                </div>
                                <input type="number" class="form-control" id="salary-amount-2"
                                    name="salary_amount_2" disabled value={{ $job_pr->salary_amount_2 }}>
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
                        <select disabled id="job-type" name="job_type" class="form-control select2">
                            <option value="0">Pole valitud</option>
                            <option value="According to the contract"
                                {{ $job_pr->job_type == 'According to the contract' ? 'selected' : '' }}>Vastavalt
                                lepingule</option>
                            <option value="Seasonal Employee"
                                {{ $job_pr->job_type == 'Seasonal Employee' ? 'selected' : '' }}>Hooajatöötaja
                            </option>
                        </select>
                        <span class="input-error text-danger" id="job_type_error"></span>
                    </div>
                </div>
            </div>
            <div id="job-type-comment-container"
                class="row {{ is_null($job_pr->job_type_comment) ? 'd-none' : '' }}">
                <div class="col-sm-12 col-md-4">
                    <div class="form-group">
                        <label for="job-type-comment">Kommentaar <span class="text-danger">*</span></label>
                        <textarea name="job_type_comment" id="job-type-comment" class="form-control" disabled>{{ $job_pr->job_type_comment }}</textarea>
                        <span class="input-error text-danger" id="job_type_comment_error"></span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-4">
                    <div class="form-group">
                        <label for="job-transport">Töötajate transport<span class="text-danger">*</span></label>
                        <select disabled id="job-transport" name="transport" class="form-control select2">
                            <option value="">Ei ole valitud</option>
                            <option value="Yes" {{ $job_pr->transport == 'Yes' ? 'selected' : '' }}>Jah
                            </option>
                            <option value="No" {{ $job_pr->transport == 'No' ? 'selected' : '' }}>Ei</option>
                        </select>
                        <span class="input-error text-danger" id="transport_error"></span>
                    </div>
                </div>
            </div>
            <div id="job-transport-comment-container"
                class="row {{ is_null($job_pr->transport_comment) ? 'd-none' : '' }}">
                <div class="col-sm-12 col-md-4">
                    <div class="form-group">
                        <label for="job-transport-comment">Kommentaar<span class="text-danger">*</span></label>
                        <textarea name="transport_comment" id="job-transport-comment" class="form-control" disabled>{{ $job_pr->transport_comment }}</textarea>
                        <span class="input-error text-danger" id="transport_comment_error"></span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-4">
                    <div class="form-group">
                        <label for="job-working-hours">Tööaeg <span class="text-danger">*</span></label>
                        <select disabled id="job-working-hours" name="working_hours" class="form-control select2">
                            <option value="">Ei ole valitud</option>
                            <option value="Mon-Fri" {{ $job_pr->working_hours == 'Mon-Fri' ? 'selected' : '' }}>E-R
                            </option>
                            <option value="On Schedule"
                                {{ $job_pr->working_hours == 'On Schedule' ? 'selected' : '' }}>Graafiku alusel
                            </option>
                        </select>
                        <span class="input-error text-danger" id="working_hours_error"></span>
                    </div>
                </div>
            </div>
            <div id="job-working-hours-comment-container"
                class="row {{ is_null($job_pr->working_hours_comment) ? 'd-none' : '' }}">
                <div class="col-sm-12 col-md-4">
                    <div class="form-group">
                        <label for="job-working-hours-comment">Kommentaar<span class="text-danger">*</span></label>
                        <textarea name="working_hours_comment" id="job-working-hours-comment" class="form-control" disabled>{{ $job_pr->working_hours_comment }}</textarea>
                        <span class="input-error text-danger" id="working_hours_comment_error"></span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-4">
                    <div class="form-group">
                        <label for="job-employee-clothes">Töötajate riided<span class="text-danger">*</span></label>
                        <select disabled id="job-employee-clothes" name="clothes" class="form-control select2">
                            <option value="">Ei ole valitud</option>
                            <option value="Yes" {{ $job_pr->clothes == 'Yes' ? 'selected' : '' }}>Jah</option>
                            <option value="No" {{ $job_pr->clothes == 'No' ? 'selected' : '' }}>Ei</option>
                        </select>
                        <span class="input-error text-danger" id="clothes_error"></span>
                    </div>
                </div>
            </div>
            <div id="job-employee-clothes-comment-container"
                class="row {{ is_null($job_pr->clothes_comment) ? 'd-none' : '' }}">
                <div class="col-sm-12 col-md-4">
                    <div class="form-group">
                        <label for="job-employee-clothes-comment">Kommentaar<span class="text-danger">*</span></label>
                        <textarea name="clothes_comment" id="job-employee-clothes-comment" class="form-control" disabled>{{ $job_pr->clothes_comment }}</textarea>
                        <span class="input-error text-danger" id="clothes_comment_error"></span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-4">
                    <div class="form-group">
                        <label for="job-shifts">Töö mitmes vahetuses <span class="text-danger">*</span></label>
                        <select disabled id="job-shifts" name="shifts" class="form-control select2">
                            <option value="">Ei ole valitud</option>
                            <option value="Yes" {{ $job_pr->shifts == 'Yes' ? 'selected' : '' }}>Jah</option>
                            <option value="No" {{ $job_pr->shifts == 'No' ? 'selected' : '' }}>Ei</option>
                        </select>
                        <span class="input-error text-danger" id="shifts_error"></span>
                    </div>
                </div>
            </div>
            <?php $shifts_array = $job_pr->shifts()->get(); ?>
            <div id="job-shifts-comment-container" class="row {{ $shifts_array->isNotEmpty() ? '' : 'd-none' }}">
                <div class="col-sm-12 col-md-12">
                    <?php $shifts_array = $job_pr->shifts()->get(); ?>
                    <?php if($shifts_array->isNotEmpty()){ ?>
                    <?php foreach ($shifts_array as $key => $shift) { ?>
                    <div class="row">
                        <div class="col-sm-12 col-md-3">
                            <div class="form-group">
                                <label for="job-shifts-{{ $key }}-start-time">{{ $key + 1 }}. vahetuse
                                    tööpäeva algus<span class="text-danger">*</span></label>
                                <input type="time" class="form-control"
                                    id="job-shifts-{{ $key }}-start-time"
                                    name="shifts_data.{{ $key }}.start_time" aria-describedby="deadlineHelp"
                                    placeholder="Vali kellaaeg" disabled value="{{ $shift->start_time }}">
                                {{-- <span class="input-error text-danger" id="_error"></span> --}}
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-3">
                            <div class="form-group">
                                <label for="job-shifts-{{ $key }}-end-time">{{ $key + 1 }}. vahetuse
                                    tööpäeva lõpp<span class="text-danger">*</span></label>
                                <input type="time" class="form-control"
                                    id="job-shifts-{{ $key }}-end-time" name="shifts_data.${index}.end_time"
                                    aria-describedby="deadlineHelp" placeholder="Vali kellaaeg" disabled
                                    value="{{ $shift->end_time }}">
                                {{-- <span class="input-error text-danger" id="_error"></span> --}}
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                    <?php }else {?>
                    <div class="row">
                        <div class="col-sm-12 col-md-3">
                            <div class="form-group">
                                <label for="job-shifts-1-start-time">1. vahetuse tööpäeva algus<span
                                        class="text-danger">*</span></label>
                                <input type="time" class="form-control" id="job-shifts-1-start-time"
                                    name="shifts_data.${index}.start_time" aria-describedby="deadlineHelp"
                                    placeholder="Vali kellaaeg">
                                {{-- <span class="input-error text-danger" id="_error"></span> --}}
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-3">
                            <div class="form-group">
                                <label for="job-shifts-1-end-time">1. vahetuse tööpäeva lõpp<span
                                        class="text-danger">*</span></label>
                                <input type="time" class="form-control" id="job-shifts-1-end-time"
                                    name="shifts_data.${index}.end_time" aria-describedby="deadlineHelp"
                                    placeholder="Vali kellaaeg">
                                {{-- <span class="input-error text-danger" id="_error"></span> --}}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-3">
                            <div class="form-group">
                                <label for="job-shifts-2-start-time">2. vahetuse tööpäeva algus<span
                                        class="text-danger">*</span></label>
                                <input type="time" class="form-control" id="job-shifts-2-start-time"
                                    name="shifts_data.${index}.start_time" aria-describedby="deadlineHelp"
                                    placeholder="Select Time">
                                {{-- <span class="input-error text-danger" id="_error"></span> --}}
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-3">
                            <div class="form-group">
                                <label for="job-shifts-2-end-time">2. vahetuse tööpäeva lõpp<span
                                        class="text-danger">*</span></label>
                                <input type="time" class="form-control" id="job-shifts-2-end-time"
                                    name="shifts_data.${index}.end_time" aria-describedby="deadlineHelp"
                                    placeholder="Select Time">
                                {{-- <span class="input-error text-danger" id="shifts_data_error"></span> --}}
                            </div>
                        </div>
                    </div>
                    <?php }?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-6">
                    <div class="form-group">
                        <label for="job-recess">Lõuna ja puhkeaeg</label>
                        <input type="text" name="recess" id="job-recess" class="form-control" disabled
                            value="{{ $job_pr->jobs->first()->recess }}">
                        <span class="input-error text-danger" id="_error"></span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-6">
                    <div class="form-group">
                        <label for="job-requirements">Nõuded töötajate tervisele, teadmistele, oskustele, võimetele,
                            isikuomadustele</label>
                        <textarea name="requirements" id="job-requirements" class="form-control" disabled>{{ $job_pr->jobs->first()->requirements }}</textarea>
                        <span class="input-error text-danger" id="requirements_error"></span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-6">
                    <div class="form-group">
                        <label for="job-comments">Lisainfo</label>
                        <textarea name="comments" id="job-comments" class="form-control" disabled>{{ $job_pr->jobs->first()->comments }}</textarea>
                        <span class="input-error text-danger" id="_error"></span>
                    </div>
                </div>
            </div>
            {{-- <div class="row">
                <div class="col-sm-12 col-md-6">
                    <div class="form-group">
                        <label for="job-files">Laadi üles ametijuhend</label><br />
                        <input type="file" className="form-control" id="job-files" name="file" multiple />
                        <span class="input-error text-danger" id="file_error"></span>
                    </div>
                </div>
            </div> --}}
            <div class="row">
                <h6>Files</h6>
                <div class="col-sm-12 col-md-6">
                    {{-- <div class="row">
                        <div class="col-sm-12 col-md-6"> --}}
                            <ul>
                        <?php
                        if ($job_pr->files->isNotEmpty()) {
                            foreach ($job_pr->files as $key => $file) {
                                echo '<li>' . $file->file_name . ' <a href="'. route('jobs.file.download', ['id' => $file->id]) .'"><i class="fa fa-download"></i></a></li>';
                            }
                        }
                        ?>
                        </ul>
                        {{-- </div>
                    </div> --}}
                </div>
                <div class="col-sm-12 col-md-6">
                    <div class="form-group">
                        <label for="job-files">Laadi üles ametijuhend</label><br/>
                        <input type="file" className="form-control" id="job-files" name="files[]" multiple/> <br/>
                        <span class="text-info">Lubatud formaadid: docx, xls, csv, zip, pdf, jpg, jpeg, png</span>
                        <span class="input-error text-danger" id="files_error"></span>
                    </div>
                </div>
            </div>
            {{-- <div class="row">
                <div class="col-sm-12 col-md-6">
                    <div class="form-group">
                        <button type="button" class="btn btn-primary" data-toggle="modal"
                            data-target="#video-modal">
                            Lisa video
                        </Button>
                    </div>
                </div>
            </div> --}}
            <div class="row">
                <h6>Videos</h6>
                <div class="col-sm-12 col-md-12" id="videos-container">
                    <div class="row">
                        <?php 
                            if($job_pr->videos->isNotEmpty()){
                                foreach ($job_pr->videos as $key => $video) {
                                    if($video->type == 1){
                            ?>
                        <div class="col-sm-12 col-md-4">
                            <video width="320" height="240" controls>
                                <source src="{{ $video->path }}">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                        <?php }elseif ($video->type == 2){ ?>
                        <div class="col-sm-12 col-md-4">
                            {{-- <iframe width="320" height="240" src="{{ $video->link }}" frameborder="0"
                                allowfullscreen></iframe> --}}
                            <iframe width="320" height="240" src="{{ $video->link }}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                        </div>

                        <?php 
                                }
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>

            {{-- <div class="modal fade" id="video-modal" tabindex="-1" role="dialog"
                aria-labelledby="video-modal-Label" aria-hidden="true">
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
                                <button data-dismiss="modal" data-toggle="modal" data-target="#video-modal-link"
                                    type="button" style="font-size: 14px;" class="mr-3 p-3 btn btn-primary">YouTube
                                    link</button>
                                <button data-dismiss="modal" data-toggle="modal" data-target="#video-modal-file"
                                    type="button" style="font-size: 14px;" class="p-3 btn btn-primary">Lae üles
                                    videofail</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}

            {{-- <div class="modal fade" id="video-modal-link" tabindex="-1" role="dialog"
                aria-labelledby="video-modal-link-Label" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">YouTube link</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input id="input-video-link-value" placeholder="Enter YouTube Link"
                                style="font-size: 14px;" type="text" class="form-control">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" id="add-video-link">Salvesta</button>
                        </div>
                    </div>
                </div>
            </div> --}}

            {{-- <div class="modal fade" id="video-modal-file" tabindex="-1" role="dialog"
                aria-labelledby="video-modal-file-Label" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Lae üles failid</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input id="input-video-file-value" style="font-size: 14px;" type="file"
                                class="form-control">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" id="add-video-file">Salvesta</button>
                        </div>
                    </div>
                </div>
            </div> --}}

            <div class="row">
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
    <script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
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

            function scrollToTop() {
                $(window).scrollTop(0);
            }

            // $('button#add-video-link').click(function() {
            //     let link = $('input#input-video-link-value').val();

            //     let videos_container = $('#videos-container');
            //     let index = videos_container.children().length;

            //     let html_output = '<div class="row">';
            //     html_output += '<p>' + link + '</p>';
            //     html_output += '<div class="d-none">';
            //     html_output += '<input type="text" value="2" name="video[' + index + '][type]">';
            //     html_output += '<input type="file" value="" name="video[' + index + '][file]">';
            //     html_output += '<input type="text" value="' + link + '" name="video[' + index +
            //         '][link]"> ';
            //     html_output += '</div>';
            //     html_output += '</div>';

            //     $(videos_container).append(html_output);
            //     $('input#input-video-link-value').val('');
            //     $('#video-modal-link').modal('hide');
            // });
            // $('button#add-video-file').click(function() {
            //     let file_name = $('input#input-video-file-value').val();
            //     let file = $('input#input-video-file-value').prop('files');

            //     let videos_container = $('#videos-container');
            //     let index = videos_container.children().length;

            //     let html_output = '<div class="row">';
            //     html_output += '<p>' + file_name.replace(/^.*[\\\/]/, '') + '</p>';
            //     html_output += '<div class="d-none">';
            //     html_output += '<input type="text" value="1" name="video[' + index + '][type]">';
            //     html_output += '<input type="file" value="' + file_name + '" name="video[' + index +
            //         '][file]">';
            //     html_output += '<input type="text" value="" name="video[' + index + '][link]"> ';
            //     html_output += '</div>';
            //     html_output += '</div>';

            //     $(videos_container).append(html_output);

            //     console.log($('input[name="video[' + index + '][file]"]').prop('files', file));
            //     console.log($('input[name="video[' + index + '][file]"]').prop('files'));

            //     $('input#input-video-file-value').val(null);
            //     $('#video-modal-file').modal('hide');
            // });

            $('.select2').select2();

            // $('#job-desired-language').change(function(e) {
            //     if ($(this).val().includes('')) {
            //         $('#job-comments-desired-language-div').removeClass('d-none')
            //     } else {
            //         $('#job-comments-desired-language-div').addClass('d-none')
            //     }
            // })

            // $('#job-salary-type').change(function(e) {
            //     if ($(this).val() == 'Number') {
            //         $('#salary-1-label').html('Enter Amount');
            //         $('#salary-1-container').removeClass('d-none');
            //         $('#salary-2-container').addClass('d-none');
            //     } else if ($(this).val() == 'Range') {
            //         $('#salary-1-label').html('Alates');
            //         $('#salary-1-container').removeClass('d-none');
            //         $('#salary-2-container').removeClass('d-none');
            //     } else {
            //         $('#salary-1-label').html('Enter Amount');
            //         $('#salary-1-container').addClass('d-none');
            //         $('#salary-2-container').addClass('d-none');
            //     }
            // });

            // $('#job-type').change(function() {
            //     if ($(this).val() == 'seasonal employee') {
            //         $('#job-type-comment-container').removeClass('d-none');
            //     } else {
            //         $('#job-type-comment-container').addClass('d-none');
            //     }
            // });

            // $('#job-transport').change(function() {
            //     if ($(this).val() == 'Yes') {
            //         $('#job-transport-comment-container').removeClass('d-none');
            //     } else {
            //         $('#job-transport-comment-container').addClass('d-none');
            //     }
            // });

            // $('#job-working-hours').change(function() {
            //     if ($(this).val() == 'On Schedule') {
            //         $('#job-working-hours-comment-container').removeClass('d-none');
            //     } else {
            //         $('#job-working-hours-comment-container').addClass('d-none');
            //     }
            // });

            // $('#job-employee-clothes').change(function() {
            //     if ($(this).val() == 'Yes') {
            //         $('#job-employee-clothes-comment-container').removeClass('d-none');
            //     } else {
            //         $('#job-employee-clothes-comment-container').addClass('d-none');
            //     }
            // });

            // $('#job-shifts').change(function() {
            //     if ($(this).val() == 'Yes') {
            //         $('#job-shifts-comment-container').removeClass('d-none');
            //     } else {
            //         $('#job-shifts-comment-container').addClass('d-none');
            //     }
            // });


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
                beforeSubmit: function(arr, $form, options) {
                    $('.input-error').html('');
                    $('#error-message-container').addClass('d-none');
                    $('#jobPostForm,#form-submit-button').prop('disabled', true);
                    $('span.spinner-border').removeClass('d-none');
                },
                success: showResponse, // post-submit callback
                error: function(response) {
                    let errors = response.responseJSON.errors;
                    for (let error in errors) {
                        let output = '<ul>';
                        for (let i = 0; i < errors[error].length; i++) {
                            output += '<li>' + errors[error][i] + '</li>';
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
                clearForm: 1,
                resetForm: 1
            };

            // bind 'myForm' and provide a simple callback function
            $('#jobPostForm').ajaxForm(options);

        });
    </script>

</body>

</html>
