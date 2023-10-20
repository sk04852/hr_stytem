import React, { useEffect, useState } from "react";
import {
  Button,
  Card,
  CardBody,
  CardHeader,
  CardTitle,
  Col,
  Row,
} from "reactstrap";
import { useHistory, useLocation, useParams } from "react-router-dom";
import { useDispatch, useSelector } from "react-redux";
import {
  getJobById,
  getJobsApplicants,
} from "../../../redux/actions/jobsAction";
import CountUp from "react-countup";
import JobsActivity from "./JobsActivity";
import ReactPlayer from "react-player";
import * as moment from "moment/moment";
import Toast from "../../constants/toast";
import JobApplicants from "./jobApplicants";
import CandidateLists from "./CandidateLists";
// import ImageViewer from "react-simple-image-viewer";
import FilteredCandidates from "./FilteredCadidates";
import { baseURL } from "../../Shared/baseURL";
import { useTranslation } from "react-i18next";

const ViewJobs = (props) => {
  const { id } = useParams();
  const dispatch = useDispatch();
  const history = useHistory();
  const location = useLocation();

  const { t } = useTranslation();

  const [itemById, setItemById] = useState("");
  const [userPermission, setUserPermission] = useState([]);
  const [countData, setCountData] = useState(0);
  const [candidatesFilter, setCandidatesFilter] = useState(false);
  const [deadline, setDeadline] = useState(null);
  const [videoURL, setVideoURL] = useState([]);
  const [files, setFiles] = useState([]);

  const [filteredCandidates, setFilteredCandidates] = useState([]);
  const [totalApplicants, setTotalApplicants] = useState([]);
  const [applicantsSorting, setApplicantsSorting] = useState(null);
  // PAGINATION STATES START
  const [offset, setOffset] = useState(0);
  const [pageNumber, setPageNumber] = useState(1);
  const [totalRecords, setTotalRecords] = useState(0);
  // PAGINATION STATES END

  const [triggerUpdate, setTriggerUpdate] = useState({});

  const getUserProfileSelector = useSelector(
    (state) => state.users.user_profile.data
  );
  const settingsSelector = useSelector((state) => state.settings);
  const jobsSelector = useSelector((state) => state.jobs);

  useEffect(() => {
    if (getUserProfileSelector) {
      setUserPermission(getUserProfileSelector.permissions);
    }

    if (jobsSelector.jobs_applicants.statistics) {
      setCountData(jobsSelector.jobs_applicants.statistics);
    }
  }, [getUserProfileSelector, jobsSelector.jobs_applicants.statistics]);

  useEffect(() => {
    const fetchJobByID = async () => {
      try {
        const response = await dispatch(getJobById(id));
        let jobResponse = response.payload.job;
        setItemById(jobResponse);

        // FILES
        let filesResponse = jobResponse.files;
        if (filesResponse.length > 0) {
          setFiles(filesResponse);
        }
      } catch (e) {
        throw e;
      }
    };
    fetchJobByID();
  }, [id]);

  useEffect(() => {
    window.scrollTo(0, 0);
  }, []);

  useEffect(() => {
    // Calculate Deadline
    let given = moment(itemById.deadline, "YYYY-MM-DD");
    let current = moment().startOf("day");
    let deadlineDays = moment.duration(given.diff(current)).asDays();
    if (deadlineDays > 0) {
      setDeadline(deadlineDays);
    } else {
      setDeadline(0);
    }

    // video links
    let videoFiles = [];
    if (itemById && itemById.videos.length > 0) {
      itemById.videos.map((item) => {
        if (item.link !== null) {
          videoFiles.push(item.link);
        }
        if (item.path !== null) {
          videoFiles.push(item.path);
        }
      });
      setVideoURL(videoFiles);
    }
  }, [itemById.deadline, itemById.videos]);

  const fetchJobsApplicants = async (candidateActionStatus) => {
    try {
      const response = await dispatch(
        getJobsApplicants(id, applicantsSorting, candidateActionStatus)
      );
      let applicantsResponse = response.payload.candidates.data;
      let totalApplicants = response.payload.candidates.data.length;
      let totalRecords = response.payload.candidates.total;
      setFilteredCandidates(applicantsResponse);
      setTotalApplicants(totalApplicants);
      setTotalRecords(totalRecords);
    } catch (error) {
      throw error;
    }
  };

  useEffect(() => {
    fetchJobsApplicants();
  }, [applicantsSorting]);

  const RenderJobDetails = () => {
    return (
      <Col lg={12} md={12}>
        <Card>
          <CardHeader>
            <CardTitle>
              <h5
                style={{
                  fontSize: `${
                    settingsSelector.FontSize === "Large"
                      ? "1.75rem"
                      : settingsSelector.FontSize === "Extra Large"
                      ? "2.25rem"
                      : "1.25rem"
                  }`,
                }}
              >
                <strong>{t("jobs.jobOfferDetails")}</strong>
              </h5>
            </CardTitle>
          </CardHeader>
          <CardBody>
            {itemById &&
              itemById.status === 3 &&
              itemById.deadline !== null && (
                <Row>
                  <Col sm={12} md={12} className="d-flex justify-content-end">
                    <Button
                      type="button"
                      color="primary"
                      className="mb-3"
                      onClick={() =>
                        history.push(
                          `/hr-jobs/view/${id}/send-verification-link/${itemById.company_pr_id}`
                        )
                      }
                    >
                      {t("jobs.verifyLinkButton")}
                    </Button>
                  </Col>
                </Row>
              )}
            <Row>
              <Col sm={4} md={4}>
                <h6
                  style={{
                    fontSize: `${
                      settingsSelector.FontSize === "Large"
                        ? "large"
                        : settingsSelector.FontSize === "Extra Large"
                        ? "x-large"
                        : "1rem"
                    }`,
                  }}
                >
                  <strong>{t("jobs.addNewJob.offerName")}</strong>
                </h6>
                <p> {itemById.jobs ? itemById.jobs[0].offer_name : ""} </p>
              </Col>
              <Col sm={4} md={4}>
                <h6
                  style={{
                    fontSize: `${
                      settingsSelector.FontSize === "Large"
                        ? "large"
                        : settingsSelector.FontSize === "Extra Large"
                        ? "x-large"
                        : "1rem"
                    }`,
                  }}
                >
                  <strong>{t("jobs.addNewJob.status.status")}</strong>
                </h6>
                <p>
                  {itemById.status == "1" ? (
                    <>{t("jobs.addNewJob.status.active")}</>
                  ) : itemById.status == "2" ? (
                    <>{t("jobs.addNewJob.status.inActive")}</>
                  ) : itemById.status == "3" ? (
                    <>{t("jobs.addNewJob.status.onHold")}</>
                  ) : (
                    "---"
                  )}
                </p>
              </Col>
              <Col sm={4} md={4}>
                <h6
                  style={{
                    fontSize: `${
                      settingsSelector.FontSize === "Large"
                        ? "large"
                        : settingsSelector.FontSize === "Extra Large"
                        ? "x-large"
                        : "1rem"
                    }`,
                  }}
                >
                  <strong>{t("jobs.addNewJob.traning")}</strong>
                </h6>
                <p>
                  {itemById.training == "1" ? (
                    <>{t("jobs.dropdownOptions.yes")}</>
                  ) : itemById.training == "0" ? (
                    <>{t("jobs.dropdownOptions.no")}</>
                  ) : (
                    ""
                  )}
                </p>
              </Col>
            </Row>
            <Row>
              <Col sm={4} md={4}>
                <h6
                  style={{
                    fontSize: `${
                      settingsSelector.FontSize === "Large"
                        ? "large"
                        : settingsSelector.FontSize === "Extra Large"
                        ? "x-large"
                        : "1rem"
                    }`,
                  }}
                >
                  <strong>{t("jobs.addNewJob.company.companyName")}</strong>
                </h6>
                <p>
                  {itemById.companyPr ? itemById.companyPr.company[0].name : ""}
                </p>
              </Col>
              <Col sm={4} md={4}>
                <h6
                  style={{
                    fontSize: `${
                      settingsSelector.FontSize === "Large"
                        ? "large"
                        : settingsSelector.FontSize === "Extra Large"
                        ? "x-large"
                        : "1rem"
                    }`,
                  }}
                >
                  <strong>{t("jobs.addNewJob.location")}</strong>
                </h6>
                <p> {itemById.jobs ? itemById.jobs[0].location : ""} </p>
              </Col>
              <Col sm={4} md={4}>
                <h6
                  style={{
                    fontSize: `${
                      settingsSelector.FontSize === "Large"
                        ? "large"
                        : settingsSelector.FontSize === "Extra Large"
                        ? "x-large"
                        : "1rem"
                    }`,
                  }}
                >
                  <strong>{t("jobs.addNewJob.creator")}</strong>
                </h6>
                <p> {itemById.creator} </p>
              </Col>
            </Row>
            <Row>
              <Col sm={4} md={4}>
                <h6
                  style={{
                    fontSize: `${
                      settingsSelector.FontSize === "Large"
                        ? "large"
                        : settingsSelector.FontSize === "Extra Large"
                        ? "x-large"
                        : "1rem"
                    }`,
                  }}
                >
                  <strong>{t("jobs.addNewJob.personContactName")}</strong>
                </h6>
                <p>{itemById.contact_name}</p>
              </Col>
              <Col sm={4} md={4}>
                <h6
                  style={{
                    fontSize: `${
                      settingsSelector.FontSize === "Large"
                        ? "large"
                        : settingsSelector.FontSize === "Extra Large"
                        ? "x-large"
                        : "1rem"
                    }`,
                  }}
                >
                  <strong>{t("jobs.addNewJob.personContactEmail")}</strong>
                </h6>
                <p>{itemById.contact_email}</p>
              </Col>
              <Col sm={4} md={4}>
                <h6
                  style={{
                    fontSize: `${
                      settingsSelector.FontSize === "Large"
                        ? "large"
                        : settingsSelector.FontSize === "Extra Large"
                        ? "x-large"
                        : "1rem"
                    }`,
                  }}
                >
                  <strong>{t("jobs.addNewJob.personContactPhone")}</strong>
                </h6>
                <p>{itemById.contact_number}</p>
              </Col>
            </Row>
            <Row>
              <Col sm={4} md={4}>
                <h6
                  style={{
                    fontSize: `${
                      settingsSelector.FontSize === "Large"
                        ? "large"
                        : settingsSelector.FontSize === "Extra Large"
                        ? "x-large"
                        : "1rem"
                    }`,
                  }}
                >
                  <strong>{t("jobs.addNewJob.jobDescription")}</strong>
                </h6>
                <p className={"text-justify"}>
                  {itemById.jobs
                    ? itemById.jobs.length > 0 && itemById.jobs[0].description
                    : ""}
                </p>
              </Col>
              <Col sm={4} md={4}>
                <h6
                  style={{
                    fontSize: `${
                      settingsSelector.FontSize === "Large"
                        ? "large"
                        : settingsSelector.FontSize === "Extra Large"
                        ? "x-large"
                        : "1rem"
                    }`,
                  }}
                >
                  <strong>{t("jobs.addNewJob.benefits")}</strong>
                </h6>
                <p className={"text-justify"}>
                  {itemById.jobs
                    ? itemById.jobs.length > 0 && itemById.jobs[0].benefits
                    : ""}
                </p>
              </Col>
              <Col sm={4} md={4}>
                <h6
                  style={{
                    fontSize: `${
                      settingsSelector.FontSize === "Large"
                        ? "large"
                        : settingsSelector.FontSize === "Extra Large"
                        ? "x-large"
                        : "1rem"
                    }`,
                  }}
                >
                  <strong>{t("jobs.addNewJob.numberOfPeoplesWanted")}</strong>
                </h6>
                <p>{itemById.required_candidates}</p>
              </Col>
            </Row>
            <Row>
              <Col sm={4} md={4}>
                <h6
                  style={{
                    fontSize: `${
                      settingsSelector.FontSize === "Large"
                        ? "large"
                        : settingsSelector.FontSize === "Extra Large"
                        ? "x-large"
                        : "1rem"
                    }`,
                  }}
                >
                  <strong>{t("jobs.addNewJob.observation")}</strong>
                </h6>
                <p>
                  {itemById.observation == "1"
                    ? "Jah"
                    : itemById.observation == "0"
                    ? "Ei"
                    : ""}
                </p>
              </Col>
              <Col sm={4} md={4}>
                <h6
                  style={{
                    fontSize: `${
                      settingsSelector.FontSize === "Large"
                        ? "large"
                        : settingsSelector.FontSize === "Extra Large"
                        ? "x-large"
                        : "1rem"
                    }`,
                  }}
                >
                  <strong>{t("jobs.addNewJob.salary.salary")}</strong>
                </h6>
                <p>{itemById.salary}</p>
              </Col>
              <Col sm={4} md={4}>
                <h6
                  style={{
                    fontSize: `${
                      settingsSelector.FontSize === "Large"
                        ? "large"
                        : settingsSelector.FontSize === "Extra Large"
                        ? "x-large"
                        : "1rem"
                    }`,
                  }}
                >
                  <strong>{t("jobs.addNewJob.salary.salaryType")}</strong>
                </h6>
                <p>{itemById.salary_type}</p>
              </Col>
            </Row>
            <Row>
              <Col sm={4} md={4}>
                <h6
                  style={{
                    fontSize: `${
                      settingsSelector.FontSize === "Large"
                        ? "large"
                        : settingsSelector.FontSize === "Extra Large"
                        ? "x-large"
                        : "1rem"
                    }`,
                  }}
                >
                  <strong>
                    {itemById.salary_type == "Range"
                      ? "Alates"
                      : "Töötasu (bruto)"}
                  </strong>
                </h6>
                <p>{itemById.salary_amount_1}</p>
              </Col>
              <Col sm={4} md={4}>
                {itemById.salary_type == "Range" ? (
                  <div>
                    <h6
                      style={{
                        fontSize: `${
                          settingsSelector.FontSize === "Large"
                            ? "large"
                            : settingsSelector.FontSize === "Extra Large"
                            ? "x-large"
                            : "1rem"
                        }`,
                      }}
                    >
                      <strong>{t("jobs.addNewJob.salary.until")}</strong>
                    </h6>
                    <p>{itemById.salary_amount_2}</p>
                  </div>
                ) : (
                  ""
                )}
              </Col>
              <Col sm={4} md={4}>
                <h6
                  style={{
                    fontSize: `${
                      settingsSelector.FontSize === "Large"
                        ? "large"
                        : settingsSelector.FontSize === "Extra Large"
                        ? "x-large"
                        : "1rem"
                    }`,
                  }}
                >
                  <strong>{t("jobs.addNewJob.department")}</strong>
                </h6>
                <p>{itemById.jobs ? itemById.jobs[0].department : ""}</p>
              </Col>
            </Row>
            <Row>
              <Col sm={4} md={4}>
                <h6
                  style={{
                    fontSize: `${
                      settingsSelector.FontSize === "Large"
                        ? "large"
                        : settingsSelector.FontSize === "Extra Large"
                        ? "x-large"
                        : "1rem"
                    }`,
                  }}
                >
                  <strong>{t("jobs.addNewJob.jobType.jobType")}</strong>
                </h6>
                <p>{itemById.job_type}</p>
              </Col>
              <Col sm={6} md={6}>
                {itemById.job_type_comment != "No" ? (
                  <div>
                    <h6
                      style={{
                        fontSize: `${
                          settingsSelector.FontSize === "Large"
                            ? "large"
                            : settingsSelector.FontSize === "Extra Large"
                            ? "x-large"
                            : "1rem"
                        }`,
                      }}
                    >
                      <strong>{t("jobs.addNewJob.comment")}</strong>
                    </h6>
                    <p className={"text-justify"}>
                      {itemById.job_type_comment}
                    </p>
                  </div>
                ) : (
                  ""
                )}
              </Col>
            </Row>
            <Row>
              <Col sm={4} md={4}>
                <h6
                  style={{
                    fontSize: `${
                      settingsSelector.FontSize === "Large"
                        ? "large"
                        : settingsSelector.FontSize === "Extra Large"
                        ? "x-large"
                        : "1rem"
                    }`,
                  }}
                >
                  <strong>{t("jobs.addNewJob.clothes")}</strong>
                </h6>
                <p>{itemById.clothes}</p>
              </Col>
              <Col sm={6} md={6}>
                {itemById.clothes_comment != "No" ? (
                  <div>
                    <h6
                      style={{
                        fontSize: `${
                          settingsSelector.FontSize === "Large"
                            ? "large"
                            : settingsSelector.FontSize === "Extra Large"
                            ? "x-large"
                            : "1rem"
                        }`,
                      }}
                    >
                      <strong>{t("jobs.addNewJob.comment")}</strong>
                    </h6>
                    <p className={"text-justify"}>{itemById.clothes_comment}</p>
                  </div>
                ) : (
                  ""
                )}
              </Col>
            </Row>
            <Row>
              <Col sm={4} md={4}>
                <h6
                  style={{
                    fontSize: `${
                      settingsSelector.FontSize === "Large"
                        ? "large"
                        : settingsSelector.FontSize === "Extra Large"
                        ? "x-large"
                        : "1rem"
                    }`,
                  }}
                >
                  <strong>
                    {t("jobs.addNewJob.workingHours.workingHours")}
                  </strong>
                </h6>
                <p>{itemById.working_hours}</p>
              </Col>
              <Col sm={6} md={6}>
                {itemById.working_hours_comment != "No" ? (
                  <div>
                    <h6
                      style={{
                        fontSize: `${
                          settingsSelector.FontSize === "Large"
                            ? "large"
                            : settingsSelector.FontSize === "Extra Large"
                            ? "x-large"
                            : "1rem"
                        }`,
                      }}
                    >
                      <strong>{t("jobs.addNewJob.comment")}</strong>
                    </h6>
                    <p className={"text-justify"}>
                      {itemById.working_hours_comment}
                    </p>
                  </div>
                ) : (
                  ""
                )}
              </Col>
            </Row>
            <Row>
              <Col sm={4} md={4}>
                <h6
                  style={{
                    fontSize: `${
                      settingsSelector.FontSize === "Large"
                        ? "large"
                        : settingsSelector.FontSize === "Extra Large"
                        ? "x-large"
                        : "1rem"
                    }`,
                  }}
                >
                  <strong>{t("jobs.addNewJob.workingLanguage")}</strong>
                </h6>
                <p>
                  {itemById.workinglanguages
                    ? itemById.workinglanguages.map((languageName, index) => (
                        <li key={index}>
                          {languageName.name} <span className="p-2 ml-2"></span>
                        </li>
                      ))
                    : ""}
                </p>
              </Col>
            </Row>
            <Row>
              <Col sm={4} md={4}>
                <h6
                  style={{
                    fontSize: `${
                      settingsSelector.FontSize === "Large"
                        ? "large"
                        : settingsSelector.FontSize === "Extra Large"
                        ? "x-large"
                        : "1rem"
                    }`,
                  }}
                >
                  <strong>{t("jobs.addNewJob.desiredLanguage")}</strong>
                </h6>
                <p>
                  {itemById.desiredlanguages
                    ? itemById.desiredlanguages.map(
                        (desiredLanguageName, index) => (
                          <div key={index}>
                            <li>
                              {desiredLanguageName.name}{" "}
                              <span className="p-2 ml-2"></span>
                            </li>
                            {itemById.desired_language_comment !== null && (
                              <li>Muu</li>
                            )}
                          </div>
                        )
                      )
                    : ""}
                </p>
              </Col>
              <Col sm={6} md={6}>
                <h6
                  style={{
                    fontSize: `${
                      settingsSelector.FontSize === "Large"
                        ? "large"
                        : settingsSelector.FontSize === "Extra Large"
                        ? "x-large"
                        : "1rem"
                    }`,
                  }}
                >
                  <strong>
                    {t("jobs.addNewJob.desiredLanguage")}{" "}
                    {t("jobs.addNewJob.comment")}
                  </strong>
                </h6>
                <p className={"text-justify"}>
                  {itemById.desired_language_comment}
                </p>
              </Col>
            </Row>
            <Row>
              <Col sm={4} md={4}>
                <h6
                  style={{
                    fontSize: `${
                      settingsSelector.FontSize === "Large"
                        ? "large"
                        : settingsSelector.FontSize === "Extra Large"
                        ? "x-large"
                        : "1rem"
                    }`,
                  }}
                >
                  <strong>
                    {t("jobs.addNewJob.durationType.durationType")}
                  </strong>
                </h6>
                <p>{itemById.duration_type_name}</p>
              </Col>
              <Col sm={4} md={4}>
                <h6
                  style={{
                    fontSize: `${
                      settingsSelector.FontSize === "Large"
                        ? "large"
                        : settingsSelector.FontSize === "Extra Large"
                        ? "x-large"
                        : "1rem"
                    }`,
                  }}
                >
                  <strong>
                    {t("jobs.addNewJob.employmentType.employmentType")}
                  </strong>
                </h6>
                <p>{itemById.employment_type_name}</p>
              </Col>
            </Row>
            <Row>
              <Col sm={4} md={4}>
                <h6
                  style={{
                    fontSize: `${
                      settingsSelector.FontSize === "Large"
                        ? "large"
                        : settingsSelector.FontSize === "Extra Large"
                        ? "x-large"
                        : "1rem"
                    }`,
                  }}
                >
                  <strong>{t("jobs.addNewJob.recess")}</strong>
                </h6>
                <p>{itemById.jobs ? itemById.jobs[0].recess : ""}</p>
              </Col>
              <Col sm={4} md={4}>
                <h6
                  style={{
                    fontSize: `${
                      settingsSelector.FontSize === "Large"
                        ? "large"
                        : settingsSelector.FontSize === "Extra Large"
                        ? "x-large"
                        : "1rem"
                    }`,
                  }}
                >
                  <strong>{t("jobs.addNewJob.shifts")}</strong>
                </h6>
                <p>
                  {itemById.shifts
                    ? itemById.shifts.length > 0 &&
                      itemById.shifts.map((item, index) => (
                        <div key={index}>
                          <strong>Vahetus {index + 1}.</strong>{" "}
                          {item.start_time} - {item.end_time}
                        </div>
                      ))
                    : ""}
                </p>
              </Col>
            </Row>
            <Row>
              <Col sm={4} md={4}>
                <h6
                  style={{
                    fontSize: `${
                      settingsSelector.FontSize === "Large"
                        ? "large"
                        : settingsSelector.FontSize === "Extra Large"
                        ? "x-large"
                        : "1rem"
                    }`,
                  }}
                >
                  <strong>{t("jobs.addNewJob.transport")}</strong>
                </h6>
                <p>{itemById.transport}</p>
              </Col>
              <Col sm={6} md={6}>
                {itemById.transport_comment != "No" ? (
                  <div>
                    <h6
                      style={{
                        fontSize: `${
                          settingsSelector.FontSize === "Large"
                            ? "large"
                            : settingsSelector.FontSize === "Extra Large"
                            ? "x-large"
                            : "1rem"
                        }`,
                      }}
                    >
                      <strong>{t("jobs.addNewJob.comment")}</strong>
                    </h6>
                    <p className={"text-justify"}>
                      {itemById.transport_comment}
                    </p>
                  </div>
                ) : (
                  ""
                )}
              </Col>
            </Row>
            <Row>
              <Col sm={12} md={12}>
                {itemById.jobs ? (
                  <div>
                    <h6
                      className={"text-justify"}
                      style={{
                        fontSize: `${
                          settingsSelector.FontSize === "Large"
                            ? "large"
                            : settingsSelector.FontSize === "Extra Large"
                            ? "x-large"
                            : "1rem"
                        }`,
                      }}
                    >
                      <strong>{t("jobs.addNewJob.requirements")}</strong>
                    </h6>
                    <p className={"text-justify"}>
                      {itemById.jobs ? itemById.jobs[0].requirements : ""}
                    </p>
                  </div>
                ) : (
                  ""
                )}
              </Col>
            </Row>
            <Row>
              <Col sm={12} md={12}>
                {itemById.jobs ? (
                  <div>
                    <h6
                      className={"text-justify"}
                      style={{
                        fontSize: `${
                          settingsSelector.FontSize === "Large"
                            ? "large"
                            : settingsSelector.FontSize === "Extra Large"
                            ? "x-large"
                            : "1rem"
                        }`,
                      }}
                    >
                      <strong>
                        {t("jobs.addNewJob.additionalInformation")}
                      </strong>
                    </h6>
                    <p className={"text-justify"}>
                      {itemById.jobs && itemById.jobs[0].comments !== null
                        ? itemById.jobs[0].comments.split("::")[0]
                        : ""}
                      <br />
                      <br />
                      {itemById.jobs && itemById.jobs[0].comments !== null
                        ? itemById.jobs[0].comments.split("::")[1]
                        : ""}
                    </p>
                  </div>
                ) : (
                  ""
                )}
              </Col>
            </Row>
            <Row>
              <Col sm={4} md={4}>
                <h6
                  style={{
                    fontSize: `${
                      settingsSelector.FontSize === "Large"
                        ? "large"
                        : settingsSelector.FontSize === "Extra Large"
                        ? "x-large"
                        : "1rem"
                    }`,
                  }}
                >
                  <strong>{t("jobs.addNewJob.deadline")}</strong>
                </h6>
                <p>
                  {deadline === null
                    ? itemById.deadline
                    : `${Math.round(deadline)} days`}
                </p>
              </Col>
              <Col sm={4} md={4}>
                <h6
                  style={{
                    fontSize: `${
                      settingsSelector.FontSize === "Large"
                        ? "large"
                        : settingsSelector.FontSize === "Extra Large"
                        ? "x-large"
                        : "1rem"
                    }`,
                  }}
                >
                  <strong>{t("jobs.createdAt")}</strong>
                </h6>
                <p>
                  {itemById.created_at === undefined
                    ? ""
                    : itemById.created_at.substring(0, 10)}
                </p>
              </Col>
            </Row>
            <Row>
              <Col sm={12} md={12}>
                <strong>{t("jobs.files")}</strong> <br />
                {files?.length > 0 &&
                  files?.map((file, index) => (
                    <div key={index}>
                      <strong>- </strong> {file.file_name}
                      <a
                        href={`${baseURL}jobs/file/download/${file.id}`}
                        target="_blank"
                      >
                        <button
                          className="btn btn-icon btn-sm js-sweetalert"
                          style={{
                            fontSize: `${
                              settingsSelector.FontSize === "Large"
                                ? "large"
                                : settingsSelector.FontSize === "Extra Large"
                                ? "x-large"
                                : "14px"
                            }`,
                          }}
                        >
                          <i className="fa fa-download"></i>
                        </button>
                      </a>
                    </div>
                  ))}
              </Col>
            </Row>
            <Row className={"mt-3"}>
              <Col sm={12} md={12}>
                <strong>Videos</strong> <br />
                <div className={"d-flex"}>
                  {videoURL &&
                    videoURL.map((items, index) => (
                      <Col sm={12} md={6} lg={4} key={index}>
                        <ReactPlayer
                          url={items}
                          volume={1}
                          muted={true}
                          playing={true}
                          loop={true}
                          controls={true}
                          height={"200px"}
                          width={"300px"}
                        />
                      </Col>
                    ))}
                </div>
              </Col>
            </Row>
          </CardBody>
          <div className="card-footer">
            {userPermission.includes("Can-Edit-Jobs") ? (
              <div className="row">
                <Button
                  type={"button"}
                  color={"primary"}
                  onClick={() => history.push(`/hr-jobs/edit/${id}`)}
                  style={{
                    fontSize: `${
                      settingsSelector.FontSize === "Large"
                        ? "large"
                        : settingsSelector.FontSize === "Extra Large"
                        ? "x-large"
                        : "14px"
                    }`,
                  }}
                >
                  {t("buttons.edit")}
                </Button>
              </div>
            ) : null}
          </div>
        </Card>
      </Col>
    );
  };

  const handleClick = async (value) => {
    setCandidatesFilter(true);
    fetchJobsApplicants(value);
  };

  const handleDoubleClick = () => {
    setCandidatesFilter(false);
  };

  const RenderCandidateFilter = () => {
    return (
      <div style={{ padding: "10px" }} className="row">
        <div className="col-lg-3 col-md-6">
          <div
            onClick={() => handleClick("")}
            onDoubleClick={handleDoubleClick}
          >
            <div className="card">
              <div className="grey-tint card-body w_sparkline hover_set">
                <div className="details">
                  <span>{t("candidates.totalCandidatesList")}</span>
                  <h3 className="mb-0">
                    <CountUp end={countData && countData["total"]} />
                  </h3>
                </div>
                <div className="w_chart">
                  <div id="mini-bar-chart1" className="mini-bar-chart" />
                </div>
              </div>
            </div>
          </div>
        </div>
        <div className="col-lg-3 col-md-6">
          <div
            onClick={() => handleClick(2)}
            onDoubleClick={handleDoubleClick}
            id="not"
            className="General Candidates List"
          >
            <div className="card">
              <div className="grey-tint card-body w_sparkline hover_set">
                <div className="details">
                  <span>{t("candidates.candidateApplyToJob")}</span>
                  <h3 className="mb-0">
                    <CountUp end={countData && countData[2]} />
                  </h3>
                </div>
                <div className="w_chart">
                  <div id="mini-bar-chart1" className="mini-bar-chart" />
                </div>
              </div>
            </div>
          </div>
        </div>
        <div className="col-lg-3 col-md-6">
          <div
            onClick={() => handleClick(3)}
            onDoubleClick={handleDoubleClick}
            id="not_shortlisted"
            className="shortlist"
          >
            <div className="card">
              <div className="grey-tint card-body w_sparkline hover_set">
                <div className="details">
                  <span>{t("candidates.shortlistedCandidates")}</span>
                  <h3 className="mb-0">
                    <CountUp end={countData && countData[3]} />
                  </h3>
                  {/* <span >124</span> */}
                </div>
                <div className="w_chart">
                  <span
                    // ref={this.sparkline2}
                    id="mini-bar-chart2"
                    className="mini-bar-chart"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>
        <div className="col-lg-3 col-md-6">
          <div
            onClick={() => handleClick(4)}
            onDoubleClick={handleDoubleClick}
            id="not_first_interview"
            className="first_interview"
          >
            <div className="card">
              <div className="grey-tint card-body w_sparkline hover_set">
                <div className="details">
                  <span>{t("candidates.1stInterviewCandidates")}</span>
                  <h3 className="mb-0">
                    <CountUp end={countData && countData[4]} />
                  </h3>
                </div>
                <div className="w_chart">
                  <span
                    // ref={this.sparkline4}
                    id="mini-bar-chart4"
                    className="mini-bar-chart"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>
        <div className="col-lg-3 col-md-6">
          <div
            onClick={() => handleClick(5)}
            onDoubleClick={handleDoubleClick}
            id="not_second_interview"
            className="second_interview"
          >
            <div className="card">
              <div className="grey-tint card-body w_sparkline hover_set">
                <div className="details">
                  <span>{t("candidates.2ndInterviewCandidates")}</span>
                  <h3 className="mb-0">
                    <CountUp end={countData && countData[5]} />
                  </h3>
                </div>
                <div className="w_chart">
                  <div id="mini-bar-chart1" className="mini-bar-chart" />
                </div>
              </div>
            </div>
          </div>
        </div>
        <div className="col-lg-3 col-md-6">
          <div
            onClick={() => handleClick(6)}
            onDoubleClick={handleDoubleClick}
            id="not_offer"
            className="offer"
          >
            <div className="card">
              <div className="grey-tint card-body w_sparkline hover_set">
                <div className="details">
                  <span>{t("candidates.offerCandidates")}</span>
                  <h3 className="mb-0">
                    <CountUp end={countData && countData[6]} />
                  </h3>
                </div>
                <div className="w_chart">
                  <span
                    // ref={this.sparkline2}
                    id="mini-bar-chart2"
                    className="mini-bar-chart"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>
        <div className="col-lg-3 col-md-6">
          <div
            onClick={() => handleClick(7)}
            onDoubleClick={handleDoubleClick}
            id="not_placed"
            className="placed"
          >
            <div className="card">
              <div className="grey-tint card-body w_sparkline hover_set">
                <div className="details">
                  <span>{t("candidates.placedCandidates")}</span>
                  <h3 className="mb-0">
                    <CountUp end={countData && countData[7]} />
                  </h3>
                </div>
                <div className="w_chart">
                  <span
                    // ref={this.sparkline3}
                    id="mini-bar-chart3"
                    className="mini-bar-chart"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>
        <div className="col-lg-3 col-md-6">
          <div
            onClick={() => handleClick(8)}
            onDoubleClick={handleDoubleClick}
            id="not_renewals"
            className="renewals"
          >
            <div className="card">
              <div className="grey-tint card-body w_sparkline hover_set">
                <div className="details">
                  <span>{t("candidates.rejectedCandidates")}</span>
                  <h3 className="mb-0">
                    <CountUp end={countData && countData[8]} />
                  </h3>
                </div>
                <div className="w_chart">
                  <span
                    // ref={this.sparkline4}
                    id="mini-bar-chart4"
                    className="mini-bar-chart"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>

        {/* FILTERED CANDIDATES LIST */}
        {candidatesFilter && (
          <FilteredCandidates
            filteredCandidates={filteredCandidates}
            totalRecords={totalRecords}
            setOffset={setOffset}
            pageNumber={pageNumber}
            setPageNumber={setPageNumber}
            setApplicantsSorting={setApplicantsSorting}
            fetchJobsApplicants={fetchJobsApplicants}
            setTriggerUpdate={setTriggerUpdate}
          />
        )}
      </div>
    );
  };

  return (
    <>
      {location.state && location.state.candidateJobOfferState ? (
        <Button
          color={"primary"}
          className={"mb-3 ml-3 mt-3"}
          type={"button"}
          onClick={() => history.push(location.state.candidateJobOfferState)}
          style={{
            fontSize: `${
              settingsSelector.FontSize === "Large"
                ? "large"
                : settingsSelector.FontSize === "Extra Large"
                ? "x-large"
                : "14px"
            }`,
          }}
        >
          {t("buttons.backToCandidate")}
        </Button>
      ) : (
        <Button
          color={"primary"}
          className={"mb-3 ml-3 mt-3"}
          type={"button"}
          onClick={() => history.push("/hr-jobs")}
          style={{
            fontSize: `${
              settingsSelector.FontSize === "Large"
                ? "large"
                : settingsSelector.FontSize === "Extra Large"
                ? "x-large"
                : "14px"
            }`,
          }}
        >
          {t("buttons.back")}
        </Button>
      )}
      {RenderCandidateFilter()}
      <RenderJobDetails />
      <JobsActivity job={itemById} id={parseInt(id)} />
      <JobApplicants triggerUpdate={triggerUpdate} />
      <CandidateLists />
      {/*<CandidateLists />*/}
      {/*<RenderTimeline />*/}
      {/*<RenderTask />*/}
      <Toast />
    </>
  );
};

export default ViewJobs;
