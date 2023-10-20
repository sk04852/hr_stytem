<!DOCTYPE html>
<html>
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title></title>
    <style>
        .leftbox {
            float: left;
            width: 50%;
            height: 700px;
        }

        .rightbox {
            float: right;
            width: 50%;
            height: 700px;
        }

        .particular {
            margin: 0px;
            padding: 0px;
            /*line-height: 0px;*/
            height: auto;
        }

        .job-history-section {
            /*height: 500px;*/
        }

        .job-history-section-heading {
            /*line-height: 0px;*/
        }

        .job-history-record {

        }

        .bottom-margin-less-10 {
            margin-bottom: -10px;
        }
    </style>
</head>
<body>
<div id="header">
    <h1 id="name">{{  $candidate_cv->first_name . ' ' . $candidate_cv->last_name}}</h1>
    <?php if (!is_null($candidate_cv->email) || !is_null($candidate_cv->phone)){ ?>
    <div style="line-height: 0.1px">
        <h4>Kontakt</h4>
        <?php if (!is_null($candidate_cv->email) && !empty($candidate_cv->email)){ ?>
        <a target="_blank"><p id="email">{{ $candidate_cv->email }}</p></a>
        <?php } ?>

        <?php if (!is_null($candidate_cv->phone) && !empty($candidate_cv->phone)){ ?>
        <p id="contact">{{ $candidate_cv->phone }}</p>
        <?php } ?>
    </div>
    <?php } ?>
</div>
<div>
    <div class="boxes">
        <h2 style="margin-bottom: 1px;">Isiklik info</h2>
        <div class="leftbox">
            <?php if (!is_null($candidate_cv->location) && !empty($candidate_cv->location)){ ?>
            <div class="particular address">
                <h4 class="bottom-margin-less-10">Aadress</h4>
                <p>{{$candidate_cv->location}}</p>
            </div>
            <?php } ?>
            <?php if (!is_null($candidate_cv->gender) && !empty($candidate_cv->gender)){ ?>
            <div class="particular gender">
                <h4 class="bottom-margin-less-10">Sugu</h4>
                <p>{{$candidate_cv->gender_name}}</p>
            </div>
            <?php } ?>

            <?php if (!is_null($candidate_cv->age) && !empty($candidate_cv->age)){ ?>
            <div class="particular age">
                <h4 class="bottom-margin-less-10">Vanus</h4>
                <p>{{$candidate_cv->age}}</p>
            </div>
            <?php } ?>

            <?php if (!is_null($candidate_cv->dob) && !empty($candidate_cv->dob)){ ?>
            <div class="particular date-of-birth">
                <h4 class="bottom-margin-less-10">Sünniaeg</h4>
                <p>{{dateFormat($candidate_cv->dob, 'd-M-Y')}}</p>
            </div>
            <?php } ?>

            <?php if (!is_null($candidate_cv->personal_information) && !empty($candidate_cv->personal_information)){ ?>
            <div class="particular personal-information">
                <h4 class="bottom-margin-less-10">Isiklik info</h4>
                <p>{{$candidate_cv->personal_information}}</p>
            </div>
            <?php } ?>

            <?php if (!is_null($candidate_cv->personal_code) && !empty($candidate_cv->personal_code)){ ?>
            <div class="particular personal_code">
                <h4 class="bottom-margin-less-10">Isikukood</h4>
                <p>{{$candidate_cv->personal_code}}</p>
            </div>
            <?php } ?>

            <?php if (!is_null($candidate_cv->children_qty) && !empty($candidate_cv->children_qty)){ ?>
            <div class="particular children-qty">
                <h4 class="bottom-margin-less-10">Laste arv</h4>
                <p>{{$candidate_cv->children_qty}}</p>
            </div>
            <?php } ?>

            <?php if (!is_null($candidate_cv->children_names) && !empty($candidate_cv->children_names)){ ?>
            <div class="particular children-names">
                <h4 class="bottom-margin-less-10">Laste nimed</h4>
                    <?php
                    $children_names = explode(',', $candidate_cv->children_names);
                    if (is_array($children_names) && !empty($children_names)) {
                        foreach ($children_names as $children_name) {
                            echo '<p>' . $children_name . '</p>';
                        }
                    } else {
                        echo '<p></p>';
                    }
                    ?>
            </div>
            <?php } ?>

            <?php if (!is_null($candidate_cv->marital_status) && !empty($candidate_cv->marital_status)){ ?>
            <div class="particular marital-status">
                <h4 class="bottom-margin-less-10">Perekonnaseis</h4>
                <p>{{$candidate_cv->marital_status}}</p>
            </div>
            <?php } ?>

            <?php if ($candidate_cv->nationalities->isNotEmpty()){ ?>
            <div class="particular marital-status">
                <h4 class="bottom-margin-less-10">Rahvus</h4>
                    <?php
                    $nationalities = $candidate_cv->nationalities;

                    if ($nationalities->isNotEmpty()) {
                        $temp_nationalities = [];
                        foreach ($nationalities as $nationality) {
                            array_push($temp_nationalities, $nationality->name);
                        }
                        echo '<p>' . implode(', ', $temp_nationalities) . '</p>';

                    } else {
                        echo '<p></p>';
                    }
                    ?>
            </div>
            <?php } ?>

            <?php if ($candidate_cv->driving_licenses->isNotEmpty()){ ?>
            <div class="particular driving-licenses">
                <h4 class="bottom-margin-less-10">Juhiluba</h4>
                    <?php
                    $driving_licenses = $candidate_cv->driving_licenses;

                    if ($driving_licenses->isNotEmpty()) {
//                    $table = '<table style="width:20px">';
//                    $table .= '<th>';
//                    $table .= '<td>Level<td>';
//                    $table .= '<td>Issue<td>';
//                    $table .= '<td>Expiry<td>';
//                    $table .= '</th>';
                        foreach ($driving_licenses as $key => $license) {
//                        $table .= '<tr>';
//                        $table .= '<td>'.  $license->level .'<td>';
//                        $table .= '<td>'.  $license->issue_date .'<td>';
//                        $table .= '<td>'.  $license->expiry_date .'<td>';
//                        $table .= '</tr>';
                            echo '<p>' . "$license->level - $license->issue_date - $license->expiry_date " . '</p>';
                        }
//                    $table .= '<table>';
//                    echo $table;
                    } else {
                        echo '<p></p>';
                    }
                    ?>
            </div>
            <?php } ?>
        </div>
        <div class="rightbox">
            <?php if (!is_null($candidate_cv->desired_job) && !empty($candidate_cv->desired_job)){ ?>
            <div class="particular desired-job">
                <h4 class="bottom-margin-less-10">Soovitud töökoht</h4>
                <p>{{$candidate_cv->desired_job}}</p>
            </div>
            <?php } ?>

            <?php if (!is_null($candidate_cv->desired_salary) && !empty($candidate_cv->desired_salary)){ ?>
            <div class="particular desired-salary">
                <h4 class="bottom-margin-less-10">Soovitud töötasu</h4>
                <p>{{$candidate_cv->desired_salary}}</p>
            </div>
            <?php } ?>

            <?php if ($candidate_cv->jobTypes->isNotEmpty()){ ?>
            <div class="particular job-type">
                <h4 class="bottom-margin-less-10">Töö tüüp</h4>
                <?php foreach ($candidate_cv->jobTypes as $type){?>
                <p><?= $type->type; ?></p>
                <?php } ?>
            </div>
            <?php } ?>
            <?php if (!is_null($candidate_cv->desired_job_time) && !empty($candidate_cv->desired_job_time)){ ?>
            <div class="particular desired-job-time">
                <h4 class="bottom-margin-less-10">Soovitud tööaeg</h4>
                <p>{{$candidate_cv->desired_job_time}}</p>
            </div>
            <?php } ?>

            <?php if (!is_null($candidate_cv->desired_job_location) && !empty($candidate_cv->desired_job_location)){ ?>
            <div class="particular desired-job-location">
                <h4 class="bottom-margin-less-10">Soovitud töö asukoht</h4>
                <p>{{$candidate_cv->desired_job_location}}</p>
            </div>
            <?php } ?>
            <?php if (!is_null($candidate_cv->keywords) && !empty($candidate_cv->keywords)){ ?>
            <div class="particular keywords">
                <h4 class="bottom-margin-less-10">Võtmesõnad</h4>
                <p>{{$candidate_cv->keywords}}</p>
            </div>
            <?php } ?>
            <?php if (!is_null($candidate_cv->mother_language) && !empty($candidate_cv->mother_language)){ ?>
            <div class="particular mother-language">
                <h4 class="bottom-margin-less-10">Emakeel</h4>
                <p>{{$candidate_cv->mother_language}}</p>
            </div>
            <?php } ?>
            <?php if (!is_null($candidate_cv->description) && !empty($candidate_cv->description)){ ?>
            <div class="particular description">
                <h4 class="bottom-margin-less-10">Kirjeldus</h4>
                <p>{{$candidate_cv->description}}</p>
            </div>
            <?php } ?>
            <?php if ($candidate_cv->tags->isNotEmpty()){ ?>
            <div class="particular tags">
                <h4 class="bottom-margin-less-10">Tags</h4>
                    <?php
                    $tags = $candidate_cv->tags;

                    if ($tags->isNotEmpty()) {
                        $temp_tags = [];
                        foreach ($tags as $tag) {
                            array_push($temp_tags, $tag->name);
                        }
                        echo '<p>' . implode(', ', $temp_tags) . '</p>';

                    } else {
                        echo '<p></p>';
                    }
                    ?>
            </div>
            <?php } ?>
        </div>
    </div>

    <div>
        <?php if ($candidate_cv->jobHistory->isNotEmpty()){ ?>
        <div class="job-history-section">
                <?php
                $job_histories = $candidate_cv->jobHistory;
                if ($job_histories->isNotEmpty()) {
                    echo '<h2 class="job-history-section-heading">Job History</h2>';
                    foreach ($job_histories as $job) {
                        echo '<div class="job-history-record">';
//                echo '<h4 class="company-name">abs' . $job->company_name . '(' . dateFormat($job->starting_time, 'd-M-Y') . '-' . (is_null($job->ending_time)) ? 'Present' : dateFormat($job->ending_time, 'd-M-Y') . ')' . '</h4>';
                        if (!is_null($job->company_name)) {
                            echo '<h4 class="company-name">' . $job->company_name . '</h4>';
                        }

                        if (!is_null($job->work_place)) {
                            echo '<p class="job-work-place">' . $job->work_place . '</p>';
                        }
                        if (!is_null($job->designation)) {
                            echo '<p class="job-title"><strong>' . $job->designation . '</strong></p>';
                        }
                        if (!is_null($job->description)) {
                            echo '<p class="job-description">' . $job->description . '</p>';
                        }
                        echo '</div>';
                    }
                }
                ?>
        </div>
        <?php } ?>
        <?php if ($candidate_cv->education->isNotEmpty()){ ?>
        </p>
        <h3>Haridus</h3>
        <table>
            <tr id="heading">
                <td>Kraad</td>
                <td>Eriala</td>
                <td>Asutus</td>
                <td>Alguskuupäev</td>
                <td>Lõppkuupäev</td>
            </tr>
                <?php
                $educations = $candidate_cv->education;
                if ($educations->isNotEmpty()) {
                    foreach ($educations as $item) {
                        echo '<tr>';
                        echo '<td>' . $item->education_level->name . '</td>';
                        echo '<td>' . $item->institute . '</td>';
                        echo '<td>' . $item->speciality . '</td>';
                        echo '<td>' . $item->starting_date . '</td>';
                        echo '<td>' . $item->ending_date . '</td>';
                        echo '</tr>';
                    }
                }
                ?>
        </table>
        <?php } ?>
        <?php if ($candidate_cv->additionalCourses->isNotEmpty()){ ?>
        </p>
        <h3>Täiendkoolitus</h3>
        <table>
            <tr id="heading">
                <td>Nimetus</td>
                <td>Kirjeldus</td>
                <td>Alguskuupäev</td>
                <td>Lõppkuupäev</td>
                <td>Maht</td>
            </tr>
                <?php
                $additional_courses = $candidate_cv->additionalCourses;
                if ($additional_courses->isNotEmpty()) {
                    foreach ($additional_courses as $item) {
                        echo '<tr>';
                        echo '<td>' . $item->title . '</td>';
                        echo '<td>' . $item->description . '</td>';
                        echo '<td>' . $item->start_date . '</td>';
                        echo '<td>' . $item->end_date . '</td>';
                        echo '<td>' . $item->total_hours . '</td>';
                        echo '</tr>';
                    }
                }
                ?>
        </table>
        <?php } ?>
        <?php if ($candidate_cv->languages->isNotEmpty()){ ?>
        </p>
        <h3>Keeled</h3>
        <ul>
                <?php
                $languages = $candidate_cv->languages;
                if ($languages->isNotEmpty()) {
                    foreach ($languages as $item) {
                        echo '<li>' . $item->name . '</li>';
                    }
                }
                ?>
        </ul>
        <?php } ?>
    </div>

</div>
<div id="footer"></div>
</body>
</html>
