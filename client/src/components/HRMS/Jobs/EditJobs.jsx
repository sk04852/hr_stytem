import React, { useEffect, useState, useRef } from "react";
import { Field, FieldArray, Form, Formik, ErrorMessage } from "formik";
import {
  Button,
  Card,
  CardBody,
  CardFooter,
  CardHeader,
  CardTitle,
  Col,
  Container,
  FormGroup,
  Input,
  Label,
  Modal,
  ModalBody,
  ModalFooter,
  ModalHeader,
  Row,
  Alert,
} from "reactstrap";
import asterik from "../../../assets/images/asterisk.png";
import { useHistory, useLocation, useParams } from "react-router-dom";
import { useDispatch, useSelector } from "react-redux";
import { disablePastDate } from "../../constants/disabledPreviousDates";
import Select from "react-select";
import { Loader } from "../../constants/loaders";
import { getAllCompanies } from "../../../redux/actions/companiesActions";
import { getJobById, updateJobs } from "../../../redux/actions/jobsAction";
import Toast from "../../constants/toast";
import {
  FormValidationErrors,
  SubmitFormValidationErrors,
} from "../../constants/errors";
import moment from "moment";
import { baseURL } from "../../Shared/baseURL";
import axios from "axios";
import { useTranslation } from "react-i18next";
import { JobValidationSchema } from "./validations/formValidations";
import {
  validateHtmlTags,
  validateYouTubeUrl,
} from "./validations/validateFuncations";
import {
  SUPPORTED_JOBS_FILES_FORMATS,
  SUPPORTED_JOBS_VIDEO_FILES_FORMATS,
} from "../../Shared/SupportedFilesExtenstions";

const EditJobs = () => {
  const { id } = useParams();
  const history = useHistory();
  const dispatch = useDispatch();
  const location = useLocation();

  const { t } = useTranslation();

  const formik_ref = useRef(null);

  let editJobsLocation = location.pathname;

  const initialJobValues = {
    company_pr_id: "",
    status: "",
    deadline: "",
    required_candidates: "",
    creator: "",
    company: "",
    contact_name: "",
    contact_email: "",
    contact_number: "",
    training: "",
    observation: "",
    desired_language_comment: "",
    salary: "",
    salary_type: "",
    salary_amount_1: "",
    salary_amount_2: "",
    job_type: "",
    job_type_comment: "",
    transport: "",
    transport_comment: "",
    working_hours: "",
    working_hours_comment: "",
    clothes: "",
    clothes_comment: "",
    shifts: "",
    offer_name: "",
    benefits: "",
    location: "",
    department: "",
    description: "",
    requirements: "",
    comments: "",
    additional_information: "",
    recess: "",
    work_language: [],
    desired_language: [],
    shifts_data: [],
    file: [],
    video: [
      {
        type: "",
        file: "",
        link: "",
      },
    ],
    old_files: [],
    duration_type: "",
    employment_type: "",
  };

  const [formValues, setFormValues] = useState(initialJobValues);
  const [itemById, setItemById] = useState("");
  const [videoModal, setVideoModal] = useState(false);
  const [videoYoutubeLink, setVideoYoutubeLink] = useState(false);
  const [nestedModal, setNestedModal] = useState(false);
  const [closeAll, setCloseAll] = useState(false);
  const [uploadFiles, setUploadFiles] = useState([]);
  const [uploadVideoFiles, setUploadVideoFiles] = useState([]);
  const [companyData, setCompanyData] = useState([]);
  const [checkCompany, setCheckCompany] = useState(false);
  const [check, setCheck] = useState(true);
  const [desiredLanguageComment, setDesiredLanguageComment] = useState(false);
  const [salary, setSalary] = useState(false);
  const [jobTypeCheck, setJobTypeCheck] = useState(false);
  const [transport, setTransport] = useState(false);
  const [hours, setHours] = useState(false);
  const [clothes, setClothes] = useState(false);
  const [deleteRowId, setDeleteRowId] = useState([]);

  // ERRORS STATE
  const [errors, setErrors] = useState({});
  const [formErrors, setFormErrors] = useState({});
  const [fileTypeError, setFileTypeError] = useState(false);
  const [videoFileTypeError, setVideoFileTypeError] = useState(false);
  const [youtubeFileTypeError, setYoutubeFileTypeError] = useState(false);

  const [checkWorkingLanguage, setCheckWorkingLanguage] = useState(false);

  let [workingLanguageDefaultValues, setWorkingLanguageDefaultValues] =
    useState([]);
  let [desiredLanguageDefaultValues, setDesiredLanguageDefaultValues] =
    useState([]);

  const toggleVideoModal = () => {
    setVideoModal(!videoModal);
  };

  const toggleNested = () => {
    setNestedModal(!nestedModal);
    setCloseAll(false);
  };

  const toggleAll = () => {
    setNestedModal(!nestedModal);
    setCloseAll(true);
  };

  useEffect(() => {
    window.scrollTo(0, 0);
  }, []);

  useEffect(() => {
    const fetchCompanies = async () => {
      const response = await dispatch(getAllCompanies(1, "", "", "", "", ""));
      setCompanyData(response.payload.data.data);
    };
    fetchCompanies();
  }, []);

  const fetchJobByID = async () => {
    try {
      const response = await dispatch(getJobById(id));

      let jobResponse = response.payload.job;
      setItemById(jobResponse);

      let jobType = jobResponse.job_type;
      let jobType_comment_section = document.getElementById(
        "job-type-comment-col"
      );
      if (jobType === "Seasonal Employee") {
        jobType_comment_section.classList.remove("d-none");
        setJobTypeCheck(true);
      } else {
        jobType_comment_section.classList.add("d-none");
        setJobTypeCheck(false);
      }

      let jobSalaryType = jobResponse.salary_type;
      let salary_amount_1 = document.getElementById("salary-amount-1-col");
      let salary_amount_2 = document.getElementById("salary-amount-2-col");
      let salary_amount_1_number_title = document.getElementById(
        "salary-amount-1-number-title"
      );
      let salary_amount_1_range_title = document.getElementById(
        "salary-amount-1-range-title"
      );
      if (jobSalaryType === "Range") {
        salary_amount_1.classList.remove("d-none");
        salary_amount_1_range_title.classList.remove("d-none");
        salary_amount_1_number_title.classList.add("d-none");
        salary_amount_2.classList.remove("d-none");
        setSalary(true);
      } else if (jobSalaryType === "Number") {
        salary_amount_1.classList.remove("d-none");
        salary_amount_1_number_title.classList.remove("d-none");
        salary_amount_1_range_title.classList.add("d-none");
        salary_amount_2.classList.add("d-none");
        setSalary(false);
      } else {
        salary_amount_1.classList.add("d-none");
        salary_amount_2.classList.add("d-none");
        salary_amount_1_range_title.classList.remove("d-none");
        salary_amount_1_number_title.classList.add("d-none");
        setSalary(false);
      }

      let jobTransport = jobResponse.transport;
      let transport_comment_section = document.getElementById(
        "transport-comment-col"
      );
      if (jobTransport === "Yes") {
        transport_comment_section.classList.remove("d-none");
        setTransport(true);
      } else {
        transport_comment_section.classList.add("d-none");
        setTransport(false);
      }

      let jobWorkingHours = jobResponse.working_hours;
      let working_hours_comment_section =
        document.getElementById("working-hours-col");
      if (jobWorkingHours === "On Schedule") {
        working_hours_comment_section.classList.remove("d-none");
        setHours(true);
      } else {
        working_hours_comment_section.classList.add("d-none");
        setHours(false);
      }

      let jobClothes = jobResponse.clothes;
      let clothes_comment_section = document.getElementById(
        "clothes-comment-col"
      );
      if (jobClothes === "Yes") {
        clothes_comment_section.classList.remove("d-none");
        setClothes(true);
      } else {
        clothes_comment_section.classList.add("d-none");
        setClothes(false);
      }

      let jobWorkingShifts = jobResponse.shifts;
      let working_shifts_section =
        document.getElementById("working-shifts-col");
      let shiftsData = [];
      if (jobWorkingShifts.length > 0) {
        working_shifts_section.classList.remove("d-none");
        jobWorkingShifts.forEach((jobShifts) => {
          let shiftStartTime = moment(jobShifts.start_time, "hh:mm:ss").format(
            "hh:mm"
          );
          let shiftEndTime = moment(jobShifts.end_time, "hh:mm:ss").format(
            "hh:mm"
          );
          shiftsData.push({
            id: jobShifts.id,
            start_time: shiftStartTime,
            end_time: shiftEndTime,
          });
        });
      } else {
        working_shifts_section.classList.add("d-none");
      }

      let workLanguage = jobResponse.workinglanguages;
      let workLanguageArr = [];
      if (workLanguage.length > 0) {
        workLanguage.forEach((item) => {
          workingLanguageDefaultValues.push({
            id: item.id,
            value: item.id,
            label: item.name,
          });
          workLanguageArr.push(item.id);
        });
      }

      let desiredLanguage = jobResponse.desiredlanguages;
      let desiredLanguageArr = [];
      if (desiredLanguage.length > 0) {
        desiredLanguage.forEach((item) => {
          desiredLanguageDefaultValues.push({
            id: item.id,
            value: item.id,
            label: item.name,
          });
          desiredLanguageArr.push(item.id);
        });
      }

      if (jobResponse.desired_language_comment !== null) {
        setDesiredLanguageComment(true);
      } else {
        setDesiredLanguageComment(false);
      }

      let oldFilesId = [];
      if (jobResponse.videos.length > 0) {
        for (let oldFile of jobResponse.videos) {
          oldFilesId.push(oldFile.id);
        }
      }
      setDeleteRowId(oldFilesId);

      let commentResponse = jobResponse.jobs[0].comments;
      let comment2 =
        commentResponse?.split("::")[1] === undefined || null
          ? ""
          : commentResponse?.split("::")[1];
      let comment = "";
      if (commentResponse !== null) {
        comment = commentResponse.split("::")[0] + "\n\n" + comment2;
      }

      setFormValues({
        ...formValues,
        offer_name: jobResponse.jobs[0].offer_name,
        status: jobResponse.status,
        deadline: jobResponse.deadline,
        company_pr_id: jobResponse.company_pr_id,
        company:
          jobResponse.companyPr === null
            ? ""
            : jobResponse.companyPr.company[0].name,
        required_candidates: jobResponse.required_candidates,
        creator: jobResponse.creator,
        benefits:
          jobResponse.jobs[0].benefits === null
            ? ""
            : jobResponse.jobs[0].benefits,
        description:
          jobResponse.jobs[0].description !== null
            ? jobResponse.jobs[0].description
            : "",
        location:
          jobResponse.jobs[0].location !== null
            ? jobResponse.jobs[0].location
            : "",
        department:
          jobResponse.jobs[0].department === null
            ? ""
            : jobResponse.jobs[0].department,
        contact_name: jobResponse.contact_name,
        contact_email: jobResponse.contact_email,
        contact_number: jobResponse.contact_number,
        training: jobResponse.training,
        observation: jobResponse.observation,
        work_language: workLanguageArr,
        desired_language: desiredLanguageArr,
        desired_language_comment:
          jobResponse.desired_language_comment !== null
            ? jobResponse.desired_language_comment
            : "",
        salary: jobResponse.salary,
        salary_type: jobSalaryType,
        salary_amount_1: jobResponse.salary_amount_1,
        salary_amount_2:
          jobResponse.salary_amount_2 !== null
            ? jobResponse.salary_amount_2
            : "",
        job_type: jobType,
        job_type_comment:
          jobResponse.job_type_comment !== null
            ? jobResponse.job_type_comment
            : "",
        transport: jobResponse.transport,
        transport_comment:
          jobResponse.transport_comment !== null
            ? jobResponse.transport_comment
            : "",
        working_hours: jobResponse.working_hours,
        working_hours_comment:
          jobResponse.working_hours_comment !== null
            ? jobResponse.working_hours_comment
            : "",
        clothes: jobResponse.clothes,
        clothes_comment:
          jobResponse.clothes_comment !== null
            ? jobResponse.clothes_comment
            : "",
        shifts: jobResponse.shifts.length > 0 ? "Yes" : "No",
        shifts_data: shiftsData,
        recess:
          jobResponse.jobs[0].recess !== null ? jobResponse.jobs[0].recess : "",
        requirements:
          jobResponse.jobs[0].requirements !== null
            ? jobResponse.jobs[0].requirements
            : "",
        comments: comment !== null ? comment : "",
        old_files: oldFilesId,
        duration_type: jobResponse.duration_type,
        employment_type: jobResponse.employment_type,
      });
    } catch (e) {
      throw e;
    }
  };

  useEffect(() => {
    fetchJobByID();
  }, []);

  const AddCompany = (e) => {
    e.preventDefault();
    setCheck(false);
    localStorage.setItem("jobsTemp", JSON.stringify(formik_ref.current.values));
    history.push("/hr-companies/add-new-company", {
      update: true,
      editJobs: editJobsLocation,
    });
  };

  const revertFormValues = async () => {
    let getFormValues = JSON.parse(localStorage.getItem("jobsTemp"));
    if (getFormValues) {
      // SALARY
      let salary_amount_1 = document.getElementById("salary-amount-1-col");
      let salary_amount_2 = document.getElementById("salary-amount-2-col");
      let salary_amount_1_number_title = document.getElementById(
        "salary-amount-1-number-title"
      );
      let salary_amount_1_range_title = document.getElementById(
        "salary-amount-1-range-title"
      );
      setFormValues({
        ...formik_ref.current.values,
        salary_type: getFormValues.salary_type,
      });
      if (getFormValues.salary_type === "Number") {
        salary_amount_1.classList.remove("d-none");
        salary_amount_1_number_title.classList.remove("d-none");
        salary_amount_1_range_title.classList.add("d-none");
        salary_amount_2.classList.add("d-none");
      }

      if (getFormValues.salary_type === "Range") {
        salary_amount_1.classList.remove("d-none");
        salary_amount_1_range_title.classList.remove("d-none");
        salary_amount_1_number_title.classList.add("d-none");
        salary_amount_2.classList.remove("d-none");
      }

      if (getFormValues.salary_type === "0") {
        salary_amount_1.classList.add("d-none");
        salary_amount_2.classList.add("d-none");
        salary_amount_1_range_title.classList.remove("d-none");
        salary_amount_1_number_title.classList.add("d-none");
      }

      // JOB TYPE
      let jobType_comment_section = document.getElementById(
        "job-type-comment-col"
      );
      setFormValues({
        ...formik_ref.current.values,
        job_type: getFormValues.job_type,
      });
      if (getFormValues.job_type === "Seasonal Employee") {
        jobType_comment_section.classList.remove("d-none");
      } else {
        jobType_comment_section.classList.add("d-none");
      }

      // TRANSPORT
      let transport_comment_section = document.getElementById(
        "transport-comment-col"
      );
      if (getFormValues.transport === "Yes") {
        setFormValues({
          ...formValues,
          transport: getFormValues.transport,
          transport_comment: getFormValues.transport_comment,
        });
        transport_comment_section.classList.remove("d-none");
      } else {
        setFormValues({
          ...formValues,
          transport: getFormValues.transport,
        });
        transport_comment_section.classList.add("d-none");
      }

      // working hours
      let working_hours_comment_section =
        document.getElementById("working-hours-col");
      setFormValues({
        ...formik_ref.current.values,
        working_hours: getFormValues.working_hours,
      });
      if (getFormValues.working_hours === "On Schedule") {
        working_hours_comment_section.classList.remove("d-none");
      } else {
        working_hours_comment_section.classList.add("d-none");
      }

      // clothes comments
      let clothes_comment_section = document.getElementById(
        "clothes-comment-col"
      );
      setFormValues({
        ...formik_ref.current.values,
        clothes: getFormValues.clothes,
      });
      if (getFormValues.clothes === "Yes") {
        clothes_comment_section.classList.remove("d-none");
      } else {
        clothes_comment_section.classList.add("d-none");
      }

      // working shifts
      let working_shifts_section =
        document.getElementById("working-shifts-col");
      setFormValues({
        ...formik_ref.current.values,
        shifts: getFormValues.shifts,
      });
      if (getFormValues.shifts === "Yes") {
        working_shifts_section.classList.remove("d-none");
      } else {
        working_shifts_section.classList.add("d-none");
      }

      if (getFormValues.shifts_data.length > 0) {
      }

      setFormValues({
        ...formik_ref.current.values,
        offer_name: getFormValues.offer_name,
        status: getFormValues.status,
        deadline: getFormValues.deadline,
        required_candidates: getFormValues.required_candidates,
        benefits: getFormValues.benefits,
        description: getFormValues.description,
        location: getFormValues.location,
        department: getFormValues.department,
        training: getFormValues.training,
        observation: getFormValues.observation,
        desired_language_comment: getFormValues.desired_language_comment,
        salary: getFormValues.salary,
        salary_type: getFormValues.salary_type,
        salary_amount_1: getFormValues.salary_amount_1,
        salary_amount_2: getFormValues.salary_amount_2,
        job_type: getFormValues.job_type,
        work_language: getFormValues.work_language,
        job_type_comment: getFormValues.job_type_comment,
        transport: getFormValues.transport,
        transport_comment: getFormValues.transport_comment,
        working_hours: getFormValues.working_hours,
        working_hours_comment: getFormValues.working_hours_comment,
        clothes: getFormValues.clothes,
        clothes_comment: getFormValues.clothes_comment,
        shifts: getFormValues.shifts,
        shifts_data: getFormValues.shifts_data,
        recess: getFormValues.recess,
        requirements: getFormValues.requirements,
        comments: getFormValues.comments,
      });
    }
  };

  useEffect(() => {
    revertFormValues();
    if (location.pathname === "/hr-jobs/add-jobs") {
      setTimeout(() => {
        localStorage.removeItem("jobsTemp");
      }, 2000);
    }
  }, []);

  const jobsSelector = useSelector((state) => state.jobs);
  const settingsSelector = useSelector((state) => state.settings);

  const handleFileChange = (e) => {
    let files = e.target.files;
    for (const key in files) {
      if (Object.hasOwnProperty.call(files, key)) {
        const element = files[key];
        if (SUPPORTED_JOBS_FILES_FORMATS.includes(element.type)) {
          setFileTypeError(false);
          setUploadFiles([...uploadFiles, files]);
        } else {
          setFileTypeError(true);
        }
      }
    }
  };

  const handleSubmit = (values) => {
    // validate
    let error = {};
    error = validateHtmlTags(values, setFormErrors);

    if (formValues.work_language.length > 0) {
      setCheckWorkingLanguage(false);
    } else {
      setCheckWorkingLanguage(true);
    }

    // submit form if there is no error
    if (
      Object.keys(error).length === 0 &&
      !fileTypeError &&
      !checkWorkingLanguage &&
      !videoFileTypeError
    ) {
      const formData = new FormData();

      formData.append("job_pr_id", id);
      formData.append("job_id", itemById.jobs[0]?.id);
      formData.append("company_pr_id", values.company_pr_id);
      formData.append("status", values.status);
      formData.append("deadline", values.deadline);
      formData.append("required_candidates", values.required_candidates);
      formData.append("creator", values.creator);
      formData.append("contact_name", values.contact_name);
      formData.append("contact_email", values.contact_email);
      formData.append("contact_number", values.contact_number);
      formData.append("training", values.training);
      formData.append("observation", values.observation);
      formData.append(
        "desired_language_comment",
        values.desired_language_comment
      );
      formData.append("salary", values.salary);
      formData.append("salary_type", values.salary_type);
      formData.append("salary_amount_1", values.salary_amount_1);
      if (values.salary_amount_2 !== null) {
        formData.append("salary_amount_2", values.salary_amount_2);
      }
      formData.append("job_type", values.job_type);
      if (values.job_type_comment !== null) {
        formData.append("job_type_comment", values.job_type_comment);
      }
      formData.append("transport", values.transport);
      if (values.transport_comment !== null) {
        formData.append("transport_comment", values.transport_comment);
      }
      formData.append("working_hours", values.working_hours);
      if (values.working_hours_comment !== null) {
        formData.append("working_hours_comment", values.working_hours_comment);
      }
      formData.append("clothes", values.clothes);
      if (values.clothes_comment !== null) {
        formData.append("clothes_comment", values.clothes_comment);
      }
      formData.append("shifts", values.shifts);
      formData.append("offer_name", values.offer_name);
      formData.append(
        "benefits",
        values.benefits === null ? "" : values.benefits
      );
      formData.append(
        "location",
        values.location === null ? "" : values.location
      );
      formData.append(
        "department",
        values.department === null ? "" : values.department
      );
      formData.append(
        "description",
        values.description === null ? "" : values.description
      );
      formData.append("requirements", values.requirements);
      formData.append("comments", values.comments.replace("\n\n", "::"));
      formData.append("additional_information", values.additional_information);
      formData.append("recess", values.recess);
      formData.append("duration_type", values.duration_type);
      formData.append("employment_type", values.employment_type);

      if (formValues.work_language.length > 0) {
        for (let i = 0; i < formValues.work_language.length; i++) {
          formData.append("work_language[]", formValues.work_language[i]);
        }
      }

      if (formValues.desired_language.length > 0) {
        for (let i = 0; i < formValues.desired_language.length; i++) {
          formData.append("desired_language[]", formValues.desired_language[i]);
        }
      } 
      // else {
      //   formData.append("desired_language[]", "");
      // }

      if (values.shifts_data.length > 0) {
        for (let i = 0; i < values.shifts_data.length; i++) {
          Object.keys(values.shifts_data[i]).map((key) => {
            formData.append(
              `shifts_data[${i}][${key}]`,
              values.shifts_data[i][key]
            );
          });
        }
      } 
      // else {
      //   formData.append(`shifts_data`, formValues.shifts_data);
      // }

      // upload files
      let files = [];
      if (uploadFiles.length > 0) {
        for (let i = 0; i <= uploadFiles.length; i++) {
          files.push(uploadFiles[0][i]);
        }
      }

      if (files.length > 0) {
        for (const file of files) {
          formData.append("files[]", file);
        }
      }

      // upload video files
      for (let i = 0; i < uploadVideoFiles.length; i++) {
        Object.keys(uploadVideoFiles[i]).map((keys) => {
          formData.append(`video[${i}][${keys}]`, uploadVideoFiles[i][keys]);
        });
      }

      // for (let i = 0; i < deleteRowId.length; i++) {
      //   formData.append(`old_files[${i}][id]`, deleteRowId[i]);
      // }

      dispatch(
        updateJobs(
          formData,
          setErrors,
          fetchJobByID,
          setUploadVideoFiles,
          history,
          id
        )
      );
    }
  };

  const handleVideoChange = (e) => {
    let videoFile = e.target.files[0];
    if (SUPPORTED_JOBS_VIDEO_FILES_FORMATS.includes(videoFile.type)) {
      setVideoFileTypeError(false);
      setUploadVideoFiles([
        ...uploadVideoFiles,
        {
          type: 1,
          file: e.target.files[0],
        },
      ]);
    } else {
      setVideoFileTypeError(true);
    }
  };
  const handleYoutubeLink = (e) => {
    const youtubeUrl = e.target.value;
    let youtubeError = false;
    youtubeError = validateYouTubeUrl(youtubeUrl, youtubeError);

    if (!youtubeError) {
      setYoutubeFileTypeError(false);
      setUploadVideoFiles([
        ...uploadVideoFiles,
        {
          type: 2,
          link: e.target.value,
        },
      ]);
    } else {
      setYoutubeFileTypeError(true);
    }
  };

  const renderVideoModal = () => {
    return (
      <Modal isOpen={videoModal} toggle={toggleVideoModal}>
        <ModalHeader
          toggle={toggleVideoModal}
          style={{
            fontSize: `${
              settingsSelector.FontSize === "Large"
                ? "large"
                : settingsSelector.FontSize === "Extra Large"
                ? "x-large"
                : "15px"
            }`,
          }}
        >
          {t("jobs.addNewJob.addVideoButton")}
        </ModalHeader>
        <ModalBody>
          <Row>
            <h6
              className={"w-100 text-center"}
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
              <strong>{t("jobs.addNewJob.addVideoModal.heading")}</strong>
            </h6>
            <div
              className={
                "w-100 d-flex flex-row justify-content-center mt-3 mb-3"
              }
            >
              <Button
                type={"button"}
                color={"primary"}
                className={"mr-3 p-3"}
                onClick={() => {
                  setVideoYoutubeLink(true);
                  setNestedModal(true);
                }}
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
                {t("jobs.addNewJob.addVideoModal.youtubeLink")}
              </Button>
              <Button
                type={"button"}
                color={"primary"}
                className={"p-3"}
                onClick={() => {
                  setVideoYoutubeLink(false);
                  setNestedModal(true);
                }}
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
                {t("jobs.addNewJob.addVideoModal.uploadVideoFile")}
              </Button>
            </div>
          </Row>
          <Modal isOpen={nestedModal} toggle={toggleNested}>
            {!videoYoutubeLink ? (
              <>
                <ModalHeader
                  style={{
                    fontSize: `${
                      settingsSelector.FontSize === "Large"
                        ? "large"
                        : settingsSelector.FontSize === "Extra Large"
                        ? "x-large"
                        : "15px"
                    }`,
                  }}
                >
                  {/*Upload Video{" "}*/}
                  {t("jobs.addNewJob.addVideoModal.uploadVideoFile")}{" "}
                  <img src={asterik} height={10} width={10} className="mt-n2" />
                </ModalHeader>
                <ModalBody>
                  <form>
                    <Input
                      type={"file"}
                      name={"video"}
                      required={true}
                      onChange={(e) => handleVideoChange(e)}
                      style={{
                        fontSize: `${
                          settingsSelector.FontSize === "Large"
                            ? "large"
                            : settingsSelector.FontSize === "Extra Large"
                            ? "x-large"
                            : "14px"
                        }`,
                      }}
                    />
                    {videoFileTypeError && (
                      <Alert color={"danger"} className={"mt-2"}>
                        {t("errors.invalidVideoType")}
                      </Alert>
                    )}
                    <Alert color={"info"} className={"mt-2"}>
                      <strong>{t("alerts.allowedFormats")}:</strong> .mp4, .mov,
                      .avi, .webm, .mkv
                    </Alert>
                  </form>
                </ModalBody>
                <ModalFooter>
                  <Button
                    color="primary"
                    onClick={toggleAll}
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
                    {t("buttons.save")}
                  </Button>
                </ModalFooter>
              </>
            ) : (
              <>
                <ModalHeader
                  style={{
                    fontSize: `${
                      settingsSelector.FontSize === "Large"
                        ? "large"
                        : settingsSelector.FontSize === "Extra Large"
                        ? "x-large"
                        : "15px"
                    }`,
                  }}
                >
                  {t("jobs.addNewJob.addVideoModal.youtubeLink")}{" "}
                  <img src={asterik} height={10} width={10} className="mt-n2" />
                </ModalHeader>
                <ModalBody>
                  <form>
                    <Input
                      type={"text"}
                      placeholder={"Enter YouTube Link"}
                      name={"video"}
                      required={true}
                      onChange={(e) => handleYoutubeLink(e)}
                      style={{
                        fontSize: `${
                          settingsSelector.FontSize === "Large"
                            ? "large"
                            : settingsSelector.FontSize === "Extra Large"
                            ? "x-large"
                            : "14px"
                        }`,
                      }}
                    />
                    {youtubeFileTypeError && (
                      <Alert color="danger" className={"mt-2"}>
                        {t("errors.invalidYoutubeLink")}
                      </Alert>
                    )}
                  </form>
                  <ModalFooter>
                    <Button
                      color="primary"
                      onClick={() => {
                        toggleNested();
                        setVideoFileTypeError(false);
                      }}
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
                      {t("buttons.save")}
                    </Button>
                  </ModalFooter>
                </ModalBody>
              </>
            )}
          </Modal>
        </ModalBody>
      </Modal>
    );
  };

  const handleCompanyBlur = (e) => {
    let companyName = e.target.value;
    let findCompany = companyData.find((item) => {
      return item.company[0].name === companyName;
    });

    if (findCompany) {
      setCheckCompany(false);
      setFormValues({
        ...formik_ref.current.values,
        company: e.target.value,
        company_pr_id: findCompany.companyContacts[0].company_pr_id,
        contact_name: findCompany.companyContacts[0].name,
        contact_email: findCompany.companyContacts[0].email,
        contact_number: findCompany.companyContacts[0].phone,
        location: findCompany.companylocations[0].location,
      });
    } else {
      setCheckCompany(true);
      document.getElementById("company").scrollIntoView();
      setFormValues({
        ...formik_ref.current.values,
        company: e.target.value,
        company_pr_id: "",
        contact_name: "",
        contact_email: "",
        contact_number: "",
        location: "",
      });
    }
  };

  const handleDesiredLanguageSelectChange = (e) => {
    let desiredLanguagesIDs = [];
    e.map((item) => {
      desiredLanguagesIDs.push(item.value);
    });
    formValues.desired_language = desiredLanguagesIDs;

    // if user select muu (other) option
    if (desiredLanguagesIDs.includes("")) {
      setDesiredLanguageComment(true);
    } else {
      setDesiredLanguageComment(false);
    }
  };

  const handleWorkingLanguageSelectChange = (e) => {
    let workingLanguagesIDs = [];
    if (e.length > 0) {
      setCheckWorkingLanguage(false);
      e.map((item) => {
        workingLanguagesIDs.push(item.value);
      });
      formValues.work_language = workingLanguagesIDs;
    } else {
      setCheckWorkingLanguage(true);
    }
  };

  const handleSalaryChange = (e) => {
    let salary_amount_1 = document.getElementById("salary-amount-1-col");
    let salary_amount_2 = document.getElementById("salary-amount-2-col");
    let salary_amount_1_number_title = document.getElementById(
      "salary-amount-1-number-title"
    );
    let salary_amount_1_range_title = document.getElementById(
      "salary-amount-1-range-title"
    );
    setFormValues({
      ...formik_ref.current.values,
      salary_type: e.target.value,
    });
    if (e.target.value === "Number") {
      salary_amount_1.classList.remove("d-none");
      salary_amount_1_number_title.classList.remove("d-none");
      salary_amount_1_range_title.classList.add("d-none");
      salary_amount_2.classList.add("d-none");
      setFormValues({
        ...formValues,
        salary_type: e.target.value,
        salary_amount_2: "",
      });
      setSalary(false);
    }

    if (e.target.value === "Range") {
      salary_amount_1.classList.remove("d-none");
      salary_amount_1_range_title.classList.remove("d-none");
      salary_amount_1_number_title.classList.add("d-none");
      salary_amount_2.classList.remove("d-none");
      setFormValues({
        ...formValues,
        salary_type: e.target.value,
        salary_amount_1: formik_ref.current.value.salary_amount_1,
        salary_amount_2: formik_ref.current.value.salary_amount_2,
      });
    }

    if (e.target.value === "0") {
      salary_amount_1.classList.add("d-none");
      salary_amount_2.classList.add("d-none");
      salary_amount_1_range_title.classList.remove("d-none");
      salary_amount_1_number_title.classList.add("d-none");
      setFormValues({
        ...formValues,
        salary_type: e.target.value,
        salary_amount_1: "",
        salary_amount_2: "",
      });
      setSalary(false);
    }
  };

  const handleJobTypeChange = (e) => {
    let jobType_comment_section = document.getElementById(
      "job-type-comment-col"
    );
    setFormValues({
      ...formik_ref.current.values,
      job_type: e.target.value,
    });
    if (e.target.value === "Seasonal Employee") {
      jobType_comment_section.classList.remove("d-none");
      setJobTypeCheck(true);
      setFormValues({
        ...formValues,
        job_type: e.target.value,
        job_type_comment: formik_ref.current.value.job_type_comment,
      });
    } else {
      jobType_comment_section.classList.add("d-none");
      setJobTypeCheck(false);
      setFormValues({
        ...formValues,
        job_type: e.target.value,
        job_type_comment: "",
      });
    }
  };

  const handleTransportChange = (e) => {
    let transport_comment_section = document.getElementById(
      "transport-comment-col"
    );
    setFormValues({
      ...formik_ref.current.values,
      transport: e.target.value,
    });
    if (e.target.value === "Yes") {
      transport_comment_section.classList.remove("d-none");
      setTransport(true);
      setFormValues({
        ...formValues,
        transport: e.target.value,
        transport_comment: formik_ref.current.values.transport_comment,
      });
    } else {
      transport_comment_section.classList.add("d-none");
      setTransport(false);
      setFormValues({
        ...formValues,
        transport: e.target.value,
        transport_comment: "",
      });
    }
  };

  const handleWorkingHoursChange = (e) => {
    let working_hours_comment_section =
      document.getElementById("working-hours-col");
    setFormValues({
      ...formik_ref.current.values,
      working_hours: e.target.value,
    });
    if (e.target.value === "On Schedule") {
      working_hours_comment_section.classList.remove("d-none");
      setHours(true);
      setFormValues({
        ...formValues,
        working_hours: e.target.value,
        working_hours_comment: formik_ref.current.values.working_hours_comment,
      });
    } else {
      working_hours_comment_section.classList.add("d-none");
      setHours(false);
      setFormValues({
        ...formValues,
        working_hours: e.target.value,
        working_hours_comment: "",
      });
    }
  };

  const handleClothesChange = (e) => {
    let clothes_comment_section = document.getElementById(
      "clothes-comment-col"
    );
    setFormValues({
      ...formik_ref.current.values,
      clothes: e.target.value,
    });
    if (e.target.value === "Yes") {
      clothes_comment_section.classList.remove("d-none");
      setClothes(true);
      setFormValues({
        ...formValues,
        clothes: e.target.value,
        clothes_comment: formik_ref.current.values.working_hours_comment,
      });
    } else {
      clothes_comment_section.classList.add("d-none");
      setClothes(false);
      setFormValues({
        ...formValues,
        clothes: e.target.value,
        clothes_comment: "",
      });
    }
  };

  const handleWorkingShiftsChange = (e) => {
    const shitValue = e.target.value;
    let working_shifts_section = document.getElementById("working-shifts-col");
    setFormValues({ ...formik_ref.current.values, shifts: shitValue });
    if (e.target.value === "Yes") {
      working_shifts_section.classList.remove("d-none");
    } else {
      setFormValues({
        ...formik_ref.current.values,
        shifts: shitValue,
        shifts_data: [],
      });
      working_shifts_section.classList.add("d-none");
    }
  };

  const handleSelectedDivDelete = (index, itemID) => {
    const token = localStorage.getItem("token");
    axios.post(
      `${baseURL}api/jobs/video/delete`,
      { job_video_id: itemID },
      {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      }
    );
    document.getElementById(`id-${index}`).remove();
    setDeleteRowId((current) =>
      current.filter((files) => {
        return files !== itemID;
      })
    );
  };

  const handleSelectedImageDivDelete = (id, fileID) => {
    const token = localStorage.getItem("token");
    axios
      .get(`${baseURL}api/jobs/file/delete/${fileID}`, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      })
      .then(() => {
        document.getElementById(`id-${id}`).remove();
      });
  };

  return (
    <>
      {renderVideoModal()}
      <Container fluid={true}>
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
                {t("buttons.edit")}
              </h5>
            </CardTitle>
          </CardHeader>

          {/*ERRORS*/}
          {errors && Object.keys(errors).length !== 0 ? (
            <FormValidationErrors errors={errors} />
          ) : null}

          {/*FORM INPUT ERRORS*/}
          {formErrors && Object.keys(formErrors).length !== 0 ? (
            <SubmitFormValidationErrors formErrors={formErrors} />
          ) : null}

          <Formik
            innerRef={formik_ref}
            initialValues={formValues}
            enableReinitialize={true}
            validationSchema={JobValidationSchema}
            onSubmit={(values) => handleSubmit(values)}
            children={({ values }) => (
              <Form>
                <fieldset disabled={jobsSelector.isLoading}>
                  <CardBody>
                    <Row>
                      <Col sm={4} md={4}>
                        <FormGroup>
                          <Label
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "0.875rem"
                              }`,
                            }}
                          >
                            {t("jobs.addNewJob.offerName")}{" "}
                            {/*Job Offer's Name{" "}*/}
                            <img
                              src={asterik}
                              height={10}
                              width={10}
                              className="mt-n2"
                            />
                          </Label>
                          <Field
                            type="text"
                            className="form-control"
                            id="offer-name"
                            name={"offer_name"}
                            required
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                            }}
                          />
                          <ErrorMessage name={`offer_name`}>
                            {(msg) => (
                              <span style={{ color: "red" }}>{msg}</span>
                            )}
                          </ErrorMessage>
                        </FormGroup>
                      </Col>
                      <Col sm={4} md={4}>
                        <FormGroup>
                          <Label
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "0.875rem"
                              }`,
                            }}
                          >
                            {t("jobs.addNewJob.status.status")}{" "}
                            <img
                              src={asterik}
                              height={10}
                              width={10}
                              className="mt-n2"
                            />
                          </Label>
                          <Field
                            as={"select"}
                            className="custom-select"
                            id={"status"}
                            name="status"
                            required
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
                            <option value="1">
                              {t("jobs.addNewJob.status.active")}
                            </option>
                            <option value="2">
                              {t("jobs.addNewJob.status.inActive")}
                            </option>
                            <option value="3">
                              {t("jobs.addNewJob.status.onHold")}
                            </option>
                          </Field>
                          <ErrorMessage name={`status`}>
                            {(msg) => (
                              <span style={{ color: "red" }}>{msg}</span>
                            )}
                          </ErrorMessage>
                        </FormGroup>
                      </Col>
                      <Col sm={4} md={4}>
                        <FormGroup>
                          <Label
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "0.875rem"
                              }`,
                            }}
                          >
                            {t("jobs.addNewJob.deadline")} {/*Deadline{" "}*/}
                            <img
                              src={asterik}
                              height={10}
                              width={10}
                              className="mt-n2"
                            />
                          </Label>
                          <Field
                            type="date"
                            className="form-control"
                            id={"deadline"}
                            name={"deadline"}
                            min={disablePastDate()}
                            max={"3000-01-01"}
                            required
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                            }}
                          />
                          <ErrorMessage name={`deadline`}>
                            {(msg) => (
                              <span style={{ color: "red" }}>{msg}</span>
                            )}
                          </ErrorMessage>
                        </FormGroup>
                      </Col>
                    </Row>
                    <Row>
                      <Col sm={4} md={4}>
                        <FormGroup>
                          <Label
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "0.875rem"
                              }`,
                            }}
                          >
                            {t("jobs.addNewJob.company.companyName")}{" "}
                            {/*Company Name{" "}*/}
                            <img
                              src={asterik}
                              height={10}
                              width={10}
                              className="mt-n2"
                            />
                          </Label>
                          <Field
                            type="text"
                            className={
                              checkCompany
                                ? "form-control border-danger border text-danger"
                                : "form-control"
                            }
                            id={"company"}
                            name={"company"}
                            onBlur={(e) => handleCompanyBlur(e)}
                            required
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                            }}
                          />
                          <ErrorMessage name={`company`}>
                            {(msg) => (
                              <span style={{ color: "red" }}>{msg}</span>
                            )}
                          </ErrorMessage>
                          {checkCompany ? (
                            <>
                              <Label
                                className="text-danger"
                                style={{
                                  fontSize: `${
                                    settingsSelector.FontSize === "Large"
                                      ? "large"
                                      : settingsSelector.FontSize ===
                                        "Extra Large"
                                      ? "x-large"
                                      : "0.875rem"
                                  }`,
                                }}
                              >
                                {t(
                                  "jobs.addNewJob.company.noCompanyFoundMessage"
                                )}
                              </Label>
                            </>
                          ) : (
                            " "
                          )}
                          <Button
                            className="btn btn-primary my-2 "
                            type="button"
                            onClick={AddCompany}
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
                            {t("jobs.addNewJob.company.createNewCompanyButton")}
                          </Button>
                        </FormGroup>
                      </Col>
                      <Col sm={4} md={4}>
                        <FormGroup>
                          <Label
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "0.875rem"
                              }`,
                            }}
                          >
                            {t("jobs.addNewJob.numberOfPeoplesWanted")}{" "}
                            {/*Candidates Required{" "}*/}
                            <img
                              src={asterik}
                              height={10}
                              width={10}
                              className="mt-n2"
                            />
                          </Label>
                          <Field
                            type="number"
                            className="form-control"
                            id={"required-candidates"}
                            name={"required_candidates"}
                            required
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                            }}
                          />
                          <ErrorMessage name={`required_candidates`}>
                            {(msg) => (
                              <span style={{ color: "red" }}>{msg}</span>
                            )}
                          </ErrorMessage>
                        </FormGroup>
                      </Col>
                      <Col sm={4} md={4}>
                        <FormGroup>
                          <Label
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "0.875rem"
                              }`,
                            }}
                          >
                            {t("jobs.addNewJob.benefits")}
                          </Label>
                          <Field
                            type="text"
                            className="form-control"
                            id={"benefits"}
                            name={"benefits"}
                            placeholder={
                              "ületunnid 1.5x tasustatud, 2x aastas efektiivsusboonus”, firmapeod, sõbralik kollektiiv jne"
                            }
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                            }}
                          />
                          <ErrorMessage name={`benefits`}>
                            {(msg) => (
                              <span style={{ color: "red" }}>{msg}</span>
                            )}
                          </ErrorMessage>
                        </FormGroup>
                      </Col>
                    </Row>
                    <Row>
                      <Col sm={4} md={4}>
                        <FormGroup>
                          <Label
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "0.875rem"
                              }`,
                            }}
                          >
                            {/*Job Description{" "}*/}
                            {t("jobs.addNewJob.jobDescription")}
                          </Label>
                          <Field
                            as="textarea"
                            className="form-control"
                            id={"job-description"}
                            name={"description"}
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                            }}
                          />
                          <ErrorMessage name={`description`}>
                            {(msg) => (
                              <span style={{ color: "red" }}>{msg}</span>
                            )}
                          </ErrorMessage>
                        </FormGroup>
                      </Col>
                      <Col sm={4} md={4}>
                        <FormGroup>
                          <Label
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "0.875rem"
                              }`,
                            }}
                          >
                            {t("jobs.addNewJob.natureOfWork.workNature")}
                          </Label>
                          {/*<Label>Temporary Work Description</Label>*/}
                          <Field
                            as="select"
                            className="custom-select"
                            id={"temporary-work-nature"}
                            name={"temporary_work_nature"}
                            required
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
                            <option>
                              {t(
                                "jobs.addNewJob.natureOfWork.temporaryIncrease"
                              )}
                            </option>
                          </Field>
                          <ErrorMessage name={`temporary_work_nature`}>
                            {(msg) => (
                              <span style={{ color: "red" }}>{msg}</span>
                            )}
                          </ErrorMessage>
                        </FormGroup>
                      </Col>
                      <Col sm={4} md={4}>
                        <FormGroup>
                          <Label
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "0.875rem"
                              }`,
                            }}
                          >
                            {t("jobs.addNewJob.durationType.durationType")}{" "}
                            {/*duration type{" "}*/}
                            <img
                              src={asterik}
                              height={10}
                              width={10}
                              className="mt-n2"
                            />
                          </Label>
                          <Field
                            as="select"
                            className="custom-select"
                            id={"duration-type"}
                            name={"duration_type"}
                            required
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
                            <option value={""}>
                              {t("jobs.dropdownOptions.noneSelected")}
                            </option>
                            <option value={"1"}>
                              {t("jobs.addNewJob.durationType.permanent")}
                            </option>
                            <option value={"2"}>
                              {t("jobs.addNewJob.durationType.temporary")}
                            </option>
                          </Field>
                          <ErrorMessage name={`duration_type`}>
                            {(msg) => (
                              <span style={{ color: "red" }}>{msg}</span>
                            )}
                          </ErrorMessage>
                        </FormGroup>
                      </Col>
                    </Row>
                    <Row>
                      <Col sm={4} md={4}>
                        <FormGroup>
                          <Label
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "0.875rem"
                              }`,
                            }}
                          >
                            {t("jobs.addNewJob.location")} {/*Location{" "}*/}
                          </Label>
                          <Field
                            type="text"
                            className="form-control"
                            id={"location"}
                            name={"location"}
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                            }}
                          />
                          <ErrorMessage name={`location`}>
                            {(msg) => (
                              <span style={{ color: "red" }}>{msg}</span>
                            )}
                          </ErrorMessage>
                        </FormGroup>
                      </Col>
                      <Col sm={4} md={4}>
                        <FormGroup>
                          <Label
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "0.875rem"
                              }`,
                            }}
                          >
                            {t("jobs.addNewJob.department")}
                          </Label>
                          {/*<Label>Department</Label>*/}
                          <Field
                            type="text"
                            className="form-control"
                            id={"department"}
                            name={"department"}
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                            }}
                          />
                          <ErrorMessage name={`department`}>
                            {(msg) => (
                              <span style={{ color: "red" }}>{msg}</span>
                            )}
                          </ErrorMessage>
                        </FormGroup>
                      </Col>
                      <Col sm={4} md={4}>
                        <FormGroup>
                          <Label
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "0.875rem"
                              }`,
                            }}
                          >
                            {t("jobs.addNewJob.creator")}
                          </Label>
                          <Field
                            type="text"
                            className="form-control"
                            // value={formValues.creator}
                            id={"creator"}
                            name={"creator"}
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                            }}
                            required
                            readOnly
                          />
                          <ErrorMessage name={`creator`}>
                            {(msg) => (
                              <span style={{ color: "red" }}>{msg}</span>
                            )}
                          </ErrorMessage>
                        </FormGroup>
                      </Col>
                    </Row>
                    <Row>
                      <Col sm={4} md={4}>
                        <FormGroup>
                          <Label
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "0.875rem"
                              }`,
                            }}
                          >
                            {t("jobs.addNewJob.personContactName")}{" "}
                            {/*Job Contact Name{" "}*/}
                            <img
                              src={asterik}
                              height={10}
                              width={10}
                              className="mt-n2"
                            />
                          </Label>
                          <Field
                            type="text"
                            className="form-control"
                            id={"company-contact-name"}
                            name={"contact_name"}
                            title={
                              checkCompany
                                ? "Palun sisesta olemasolev ettevõte. See väli täidetakse automaatselt"
                                : ""
                            }
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                            }}
                            required
                          />
                          <ErrorMessage name={`contact_name`}>
                            {(msg) => (
                              <span style={{ color: "red" }}>{msg}</span>
                            )}
                          </ErrorMessage>
                        </FormGroup>
                      </Col>
                      <Col sm={4} md={4}>
                        <FormGroup>
                          <Label
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "0.875rem"
                              }`,
                            }}
                          >
                            {t("jobs.addNewJob.personContactEmail")}{" "}
                            {/*Job Contact Email{" "}*/}
                            <img
                              src={asterik}
                              height={10}
                              width={10}
                              className="mt-n2"
                            />
                          </Label>
                          <Field
                            type="email"
                            className="form-control"
                            id={"company-contact-email"}
                            name={"contact_email"}
                            title={
                              checkCompany
                                ? "Palun sisesta olemasolev ettevõte. See väli täidetakse automaatselt"
                                : ""
                            }
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                            }}
                            required
                          />
                          <ErrorMessage name={`contact_email`}>
                            {(msg) => (
                              <span style={{ color: "red" }}>{msg}</span>
                            )}
                          </ErrorMessage>
                        </FormGroup>
                      </Col>
                      <Col sm={4} md={4}>
                        <FormGroup>
                          <Label
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "0.875rem"
                              }`,
                            }}
                          >
                            {t("jobs.addNewJob.personContactPhone")}{" "}
                            {/*Job Contact Number{" "}*/}
                            <img
                              src={asterik}
                              height={10}
                              width={10}
                              className="mt-n2"
                            />
                          </Label>
                          <Field
                            type="text"
                            className="form-control"
                            id={"company-contact-contact"}
                            name={"contact_number"}
                            title={
                              checkCompany
                                ? "Palun sisesta olemasolev ettevõte. See väli täidetakse automaatselt"
                                : ""
                            }
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                            }}
                            required
                          />
                          <ErrorMessage name={`contact_number`}>
                            {(msg) => (
                              <span style={{ color: "red" }}>{msg}</span>
                            )}
                          </ErrorMessage>
                        </FormGroup>
                      </Col>
                    </Row>
                    <Row>
                      <Col sm={4} md={4}>
                        <FormGroup>
                          <Label
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "0.875rem"
                              }`,
                            }}
                          >
                            {t("jobs.addNewJob.traning")}{" "}
                            {/*Training takes place on site at the company{" "}*/}
                            <img
                              src={asterik}
                              height={10}
                              width={10}
                              className="mt-n2"
                            />
                          </Label>
                          <Field
                            as={"select"}
                            className="custom-select"
                            id={"training"}
                            name="training"
                            required
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
                            <option value={""}>
                              {t("jobs.dropdownOptions.noneSelected")}
                            </option>
                            <option value="1">
                              {t("jobs.dropdownOptions.yes")}
                            </option>
                            <option value="0">
                              {t("jobs.dropdownOptions.no")}
                            </option>
                          </Field>
                          <ErrorMessage name={`training`}>
                            {(msg) => (
                              <span style={{ color: "red" }}>{msg}</span>
                            )}
                          </ErrorMessage>
                        </FormGroup>
                      </Col>
                      <Col sm={4} md={4}>
                        <FormGroup>
                          <Label
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "0.875rem"
                              }`,
                            }}
                          >
                            {t("jobs.addNewJob.observation")}{" "}
                            {/*Possibility of Work Observation{" "}*/}
                            <img
                              src={asterik}
                              height={10}
                              width={10}
                              className="mt-n2"
                            />
                          </Label>
                          <Field
                            as={"select"}
                            className="custom-select"
                            id={"observation"}
                            name="observation"
                            required
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
                            <option value={""}>
                              {t("jobs.dropdownOptions.noneSelected")}
                            </option>
                            <option value="1">
                              {t("jobs.dropdownOptions.yes")}
                            </option>
                            <option value="0">
                              {t("jobs.dropdownOptions.no")}
                            </option>
                          </Field>
                          <ErrorMessage name={`observation`}>
                            {(msg) => (
                              <span style={{ color: "red" }}>{msg}</span>
                            )}
                          </ErrorMessage>
                        </FormGroup>
                      </Col>
                      <Col sm={4} md={4}>
                        <FormGroup>
                          <Label
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "0.875rem"
                              }`,
                            }}
                          >
                            {t("jobs.addNewJob.employmentType.employmentType")}{" "}
                            {/*employment type{" "}*/}
                            <img
                              src={asterik}
                              height={10}
                              width={10}
                              className="mt-n2"
                            />
                          </Label>
                          <Field
                            as="select"
                            className="custom-select"
                            id={"employment-type"}
                            name={"employment_type"}
                            required
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
                            <option value={""}>
                              {t("jobs.dropdownOptions.noneSelected")}
                            </option>
                            <option value={"1"}>
                              {t("jobs.addNewJob.employmentType.fullTime")}
                            </option>
                            <option value={"2"}>
                              {t("jobs.addNewJob.employmentType.partialTime")}
                            </option>
                            <option value={"3"}>
                              {t(
                                "jobs.addNewJob.employmentType.substituteWork"
                              )}
                            </option>
                          </Field>
                          <ErrorMessage name={`employment_type`}>
                            {(msg) => (
                              <span style={{ color: "red" }}>{msg}</span>
                            )}
                          </ErrorMessage>
                        </FormGroup>
                      </Col>
                    </Row>
                    <Row>
                      <Col sm={4} md={4}>
                        <FormGroup>
                          <Label
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "0.875rem"
                              }`,
                            }}
                          >
                            {t("jobs.addNewJob.desiredLanguage")}{" "}
                            {/*Desired Language{" "}*/}
                          </Label>
                          <Select
                            isMulti
                            className="basic-multi-select"
                            closeMenuOnSelect={false}
                            name={"desired_language"}
                            id={"desired_language"}
                            onChange={(e) =>
                              handleDesiredLanguageSelectChange(e)
                            }
                            defaultValue={desiredLanguageDefaultValues}
                            options={[
                              { value: "4", label: "Eesti" },
                              { value: "5", label: "Inglise" },
                              { value: "6", label: "Vene" },
                              { value: "", label: "muu" },
                            ]}
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                            }}
                          />
                        </FormGroup>
                      </Col>
                      {desiredLanguageComment ? (
                        <Col sm={6} md={6}>
                          <Label
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "0.875rem"
                              }`,
                            }}
                          >
                            {t("jobs.addNewJob.comment")}{" "}
                            <img
                              src={asterik}
                              height={10}
                              width={10}
                              className="mt-n2"
                            />
                          </Label>
                          <Field
                            as={"textarea"}
                            className="form-control"
                            id={"desired-language-comment"}
                            name={"desired_language_comment"}
                            required
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                            }}
                          />
                          <ErrorMessage name={`desired_language_comment`}>
                            {(msg) => (
                              <span style={{ color: "red" }}>{msg}</span>
                            )}
                          </ErrorMessage>
                        </Col>
                      ) : (
                        ""
                      )}
                      <Col sm={4} md={4}>
                        <FormGroup>
                          <Label
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "0.875rem"
                              }`,
                            }}
                          >
                            {t("jobs.addNewJob.workingLanguage")}{" "}
                            {/*Working Language{" "}*/}
                            <img
                              src={asterik}
                              height={10}
                              width={10}
                              className="mt-n2"
                            />
                          </Label>
                          <Select
                            isMulti
                            className="basic-multi-select"
                            name={"working_language"}
                            closeMenuOnSelect={false}
                            onChange={(e) =>
                              handleWorkingLanguageSelectChange(e)
                            }
                            defaultValue={workingLanguageDefaultValues}
                            options={[
                              { value: "4", label: "Eesti" },
                              { value: "5", label: "Inglise" },
                              { value: "6", label: "Vene" },
                            ]}
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                            }}
                          />
                          {checkWorkingLanguage && (
                            <span style={{ color: "red" }}>
                              {t("formValidationMessages.required")}
                            </span>
                          )}
                        </FormGroup>
                      </Col>
                    </Row>
                    <Row>
                      <Col sm={4} md={4}>
                        <FormGroup>
                          <Label
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "0.875rem"
                              }`,
                            }}
                          >
                            {t("jobs.addNewJob.salary.salary")}{" "}
                            {/*Salary{" "}*/}
                            <img
                              src={asterik}
                              height={10}
                              width={10}
                              className="mt-n2"
                            />
                          </Label>
                          <Field
                            as={"select"}
                            className="custom-select"
                            id={"salary"}
                            name="salary"
                            required
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
                            <option value={""}>
                              {t("jobs.dropdownOptions.noneSelected")}
                            </option>
                            <option value="Monthly">
                              {t("jobs.addNewJob.salary.salaryOptions.six")}
                            </option>
                            <option value="Hourly">
                              {t("jobs.addNewJob.salary.salaryOptions.hour")}
                            </option>
                          </Field>
                          <ErrorMessage name={`salary`}>
                            {(msg) => (
                              <span style={{ color: "red" }}>{msg}</span>
                            )}
                          </ErrorMessage>
                        </FormGroup>
                      </Col>
                      <Col sm={4} md={4}>
                        <FormGroup>
                          <Label
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "0.875rem"
                              }`,
                            }}
                          >
                            {t("jobs.addNewJob.salary.salaryType")}{" "}
                            {/*Salary Type{" "}*/}
                            <img
                              src={asterik}
                              height={10}
                              width={10}
                              className="mt-n2"
                            />
                          </Label>
                          <Field
                            as={"select"}
                            className="custom-select"
                            id={"job-salary-type"}
                            name="salary_type"
                            onChange={handleSalaryChange}
                            required
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
                            <option value={0}>
                              {t("jobs.dropdownOptions.noneSelected")}
                            </option>
                            <option value="Number">
                              {t(
                                "jobs.addNewJob.salary.salaryTypeOptions.number"
                              )}
                            </option>
                            <option value="Range">
                              {t(
                                "jobs.addNewJob.salary.salaryTypeOptions.range"
                              )}
                            </option>
                          </Field>
                          <ErrorMessage name={`salary_type`}>
                            {(msg) => (
                              <span style={{ color: "red" }}>{msg}</span>
                            )}
                          </ErrorMessage>
                        </FormGroup>
                      </Col>
                      <Col
                        className={"d-none"}
                        id={"salary-amount-1-col"}
                        sm={6}
                        md={2}
                      >
                        <FormGroup>
                          <Label
                            id={"salary-amount-1-range-title"}
                            className={"d-none"}
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "0.875rem"
                              }`,
                            }}
                          >
                            {t("jobs.addNewJob.salary.since")}{" "}
                            <img
                              src={asterik}
                              height={10}
                              width={10}
                              className="mt-n2"
                            />
                          </Label>
                          <Label
                            id={"salary-amount-1-number-title"}
                            className={"d-none"}
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "0.875rem"
                              }`,
                            }}
                          >
                            {t("jobs.addNewJob.salary.grossSalary")}{" "}
                            <img
                              src={asterik}
                              height={10}
                              width={10}
                              className="mt-n2"
                            />
                          </Label>
                          <div className="input-group mb-3">
                            <div className="input-group-prepend">
                              <span
                                className="input-group-text"
                                style={{
                                  fontSize: `${
                                    settingsSelector.FontSize === "Large"
                                      ? "large"
                                      : settingsSelector.FontSize ===
                                        "Extra Large"
                                      ? "x-large"
                                      : "0.875rem"
                                  }`,
                                }}
                              >
                                €
                              </span>
                            </div>
                            <Field
                              type={"number"}
                              className="form-control glitch"
                              id={"salary-amount-1"}
                              name={"salary_amount_1"}
                              required
                              style={{
                                fontSize: `${
                                  settingsSelector.FontSize === "Large"
                                    ? "large"
                                    : settingsSelector.FontSize ===
                                      "Extra Large"
                                    ? "x-large"
                                    : "14px"
                                }`,
                              }}
                            />
                          </div>
                          <ErrorMessage name={`salary_amount_1`}>
                            {(msg) => (
                              <span style={{ color: "red" }}>{msg}</span>
                            )}
                          </ErrorMessage>
                        </FormGroup>
                      </Col>

                      <Col
                        className={"d-none"}
                        id={"salary-amount-2-col"}
                        sm={6}
                        md={2}
                      >
                        <FormGroup>
                          <Label
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "0.875rem"
                              }`,
                            }}
                          >
                            {t("jobs.addNewJob.salary.until")}{" "}
                            <img
                              src={asterik}
                              height={10}
                              width={10}
                              className="mt-n2"
                            />
                          </Label>
                          <div className="input-group mb-3">
                            <div className="input-group-prepend">
                              <span
                                className="input-group-text"
                                style={{
                                  fontSize: `${
                                    settingsSelector.FontSize === "Large"
                                      ? "large"
                                      : settingsSelector.FontSize ===
                                        "Extra Large"
                                      ? "x-large"
                                      : "0.875rem"
                                  }`,
                                }}
                              >
                                €
                              </span>
                            </div>
                            <Field
                              type={"number"}
                              className="form-control"
                              id={"salary-amount-2"}
                              name={"salary_amount_2"}
                              required={salary ? true : false}
                              style={{
                                fontSize: `${
                                  settingsSelector.FontSize === "Large"
                                    ? "large"
                                    : settingsSelector.FontSize ===
                                      "Extra Large"
                                    ? "x-large"
                                    : "14px"
                                }`,
                              }}
                            />
                          </div>
                          <ErrorMessage name={`salary_amount_2`}>
                            {(msg) => (
                              <span style={{ color: "red" }}>{msg}</span>
                            )}
                          </ErrorMessage>
                        </FormGroup>
                      </Col>
                    </Row>
                    <Row>
                      <Col sm={4} md={4}>
                        <FormGroup>
                          <Label
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "0.875rem"
                              }`,
                            }}
                          >
                            {t("jobs.addNewJob.jobType.jobType")}{" "}
                            {/*Job Type{" "}*/}
                            <img
                              src={asterik}
                              height={10}
                              width={10}
                              className="mt-n2"
                            />
                          </Label>
                          <Field
                            as={"select"}
                            className="custom-select"
                            id={"job-type"}
                            name="job_type"
                            onChange={handleJobTypeChange}
                            required
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
                            <option value={0}>
                              {t("jobs.dropdownOptions.noneSelected")}
                            </option>
                            <option value="According to the contract">
                              {t(
                                "jobs.addNewJob.jobType.jobTypeOptions.accordingToContract"
                              )}
                            </option>
                            <option value="Seasonal Employee">
                              {t(
                                "jobs.addNewJob.jobType.jobTypeOptions.seasonalEmployee"
                              )}
                            </option>
                          </Field>
                          <ErrorMessage name={`job_type`}>
                            {(msg) => (
                              <span style={{ color: "red" }}>{msg}</span>
                            )}
                          </ErrorMessage>
                        </FormGroup>
                      </Col>
                      <Col
                        sm={6}
                        md={4}
                        className={"d-none"}
                        id={"job-type-comment-col"}
                      >
                        <FormGroup>
                          <Label
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "0.875rem"
                              }`,
                            }}
                          >
                            {t("jobs.addNewJob.comment")}
                            <img
                              src={asterik}
                              height={10}
                              width={10}
                              className="mt-n2"
                            />
                          </Label>
                          <Field
                            as={"textarea"}
                            className="form-control"
                            id={"job-type-comment"}
                            name={"job_type_comment"}
                            required={jobTypeCheck ? true : false}
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                            }}
                          />
                          <ErrorMessage name={`job_type_comment`}>
                            {(msg) => (
                              <span style={{ color: "red" }}>{msg}</span>
                            )}
                          </ErrorMessage>
                        </FormGroup>
                      </Col>
                    </Row>
                    <Row>
                      <Col sm={4} md={4}>
                        <FormGroup>
                          <Label
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "0.875rem"
                              }`,
                            }}
                          >
                            {t("jobs.addNewJob.transport")}{" "}
                            {/*Employee's Transport{" "}*/}
                            <img
                              src={asterik}
                              height={10}
                              width={10}
                              className="mt-n2"
                            />
                          </Label>
                          <Field
                            as={"select"}
                            className="custom-select"
                            id={"transport"}
                            name="transport"
                            onChange={handleTransportChange}
                            required
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
                            <option value={0}>
                              {t("jobs.dropdownOptions.noneSelected")}
                            </option>
                            <option value="Yes">
                              {t("jobs.dropdownOptions.yes")}
                            </option>
                            <option value="No">
                              {t("jobs.dropdownOptions.no")}
                            </option>
                          </Field>
                          <ErrorMessage name={`transport`}>
                            {(msg) => (
                              <span style={{ color: "red" }}>{msg}</span>
                            )}
                          </ErrorMessage>
                        </FormGroup>
                      </Col>
                      <Col
                        sm={6}
                        md={4}
                        className={"d-none"}
                        id={"transport-comment-col"}
                      >
                        <FormGroup>
                          <Label
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "0.875rem"
                              }`,
                            }}
                          >
                            {t("jobs.addNewJob.comment")}{" "}
                            <img
                              src={asterik}
                              height={10}
                              width={10}
                              className="mt-n2"
                            />
                          </Label>
                          <Field
                            as={"textarea"}
                            className="form-control "
                            id={"transport-comment"}
                            name={"transport_comment"}
                            required={transport ? true : false}
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                            }}
                          />
                          <ErrorMessage name={`transport_comment`}>
                            {(msg) => (
                              <span style={{ color: "red" }}>{msg}</span>
                            )}
                          </ErrorMessage>
                        </FormGroup>
                      </Col>
                    </Row>
                    <Row>
                      <Col sm={4} md={4}>
                        <FormGroup>
                          <Label
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "0.875rem"
                              }`,
                            }}
                          >
                            {t("jobs.addNewJob.workingHours.workingHours")}{" "}
                            {/*Working Hours{" "}*/}
                            <img
                              src={asterik}
                              height={10}
                              width={10}
                              className="mt-n2"
                            />
                          </Label>
                          <Field
                            as={"select"}
                            className="custom-select"
                            id={"working-hours"}
                            name="working_hours"
                            onChange={handleWorkingHoursChange}
                            required
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
                            <option value={""}>
                              {t("jobs.dropdownOptions.noneSelected")}
                            </option>
                            <option value="Mon-Fri">
                              {t("jobs.addNewJob.workingHours.monToFri")}
                            </option>
                            <option value="On Schedule">
                              {t("jobs.addNewJob.workingHours.schedule")}
                            </option>
                          </Field>
                          <ErrorMessage name={`working_hours`}>
                            {(msg) => (
                              <span style={{ color: "red" }}>{msg}</span>
                            )}
                          </ErrorMessage>
                        </FormGroup>
                      </Col>
                      <Col
                        sm={6}
                        md={4}
                        className={"d-none"}
                        id={"working-hours-col"}
                      >
                        <FormGroup>
                          <Label
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "0.875rem"
                              }`,
                            }}
                          >
                            {t("jobs.addNewJob.comment")}{" "}
                            <img
                              src={asterik}
                              height={10}
                              width={10}
                              className="mt-n2"
                            />
                          </Label>
                          <Field
                            as={"textarea"}
                            className="form-control"
                            id={"working-hours-comment"}
                            name={"working_hours_comment"}
                            required={hours ? true : false}
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                            }}
                          />
                          <ErrorMessage name={`working_hours_comment`}>
                            {(msg) => (
                              <span style={{ color: "red" }}>{msg}</span>
                            )}
                          </ErrorMessage>
                        </FormGroup>
                      </Col>
                    </Row>
                    <Row>
                      <Col sm={4} md={4}>
                        <FormGroup>
                          <Label
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "0.875rem"
                              }`,
                            }}
                          >
                            {t("jobs.addNewJob.clothes")}{" "}
                            {/*Employee's Clothes{" "}*/}
                            <img
                              src={asterik}
                              height={10}
                              width={10}
                              className="mt-n2"
                            />
                          </Label>
                          <Field
                            as={"select"}
                            className="custom-select glitch"
                            id={"clothes"}
                            name="clothes"
                            onChange={handleClothesChange}
                            required
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
                            <option value={0}>
                              {t("jobs.dropdownOptions.noneSelected")}
                            </option>
                            <option value="Yes">
                              {t("jobs.dropdownOptions.yes")}
                            </option>
                            <option value="No">
                              {t("jobs.dropdownOptions.no")}
                            </option>
                          </Field>
                          <ErrorMessage name={`clothes`}>
                            {(msg) => (
                              <span style={{ color: "red" }}>{msg}</span>
                            )}
                          </ErrorMessage>
                        </FormGroup>
                      </Col>
                      <Col
                        sm={6}
                        md={4}
                        className={"d-none"}
                        id={"clothes-comment-col"}
                      >
                        <FormGroup>
                          <Label
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "0.875rem"
                              }`,
                            }}
                          >
                            {t("jobs.addNewJob.comment")}{" "}
                            <img
                              src={asterik}
                              height={10}
                              width={10}
                              className="mt-n2"
                            />
                          </Label>
                          <Field
                            as={"textarea"}
                            className="form-control"
                            id={"clothes-comment"}
                            name={"clothes_comment"}
                            required={clothes ? true : false}
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                            }}
                          />
                          <ErrorMessage name={`clothes_comment`}>
                            {(msg) => (
                              <span style={{ color: "red" }}>{msg}</span>
                            )}
                          </ErrorMessage>
                        </FormGroup>
                      </Col>
                    </Row>
                    <Row>
                      <Col sm={4} md={4}>
                        <FormGroup>
                          <Label
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "0.875rem"
                              }`,
                            }}
                          >
                            {t("jobs.addNewJob.shifts")}{" "}
                            {/*Working Shifts{" "}*/}
                            <img
                              src={asterik}
                              height={10}
                              width={10}
                              className="mt-n2"
                            />
                          </Label>
                          <Field
                            as={"select"}
                            className="custom-select"
                            id={"shifts"}
                            name="shifts"
                            onChange={handleWorkingShiftsChange}
                            required
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
                            <option value={""}>
                              {t("jobs.dropdownOptions.noneSelected")}
                            </option>
                            <option value="Yes">
                              {t("jobs.dropdownOptions.yes")}
                            </option>
                            <option value="No">
                              {t("jobs.dropdownOptions.no")}
                            </option>
                          </Field>
                          <ErrorMessage name={`shifts`}>
                            {(msg) => (
                              <span style={{ color: "red" }}>{msg}</span>
                            )}
                          </ErrorMessage>
                        </FormGroup>
                      </Col>
                    </Row>
                    <FieldArray
                      name={"shifts_data"}
                      render={(arrayHelpers) => (
                        <div
                          className={"align-self-center pb-3 d-none"}
                          id={"working-shifts-col"}
                        >
                          {values.shifts_data &&
                          values.shifts_data.length > 0 ? (
                            values.shifts_data.map((shifts_data, index) => (
                              <div key={index}>
                                <Label
                                  style={{
                                    fontSize: `${
                                      settingsSelector.FontSize === "Large"
                                        ? "large"
                                        : settingsSelector.FontSize ===
                                          "Extra Large"
                                        ? "x-large"
                                        : "0.875rem"
                                    }`,
                                  }}
                                >
                                  {index + 1}. Vahetus
                                </Label>
                                <Row key={index}>
                                  <Col sm={3} md={3}>
                                    <Label
                                      style={{
                                        fontSize: `${
                                          settingsSelector.FontSize === "Large"
                                            ? "large"
                                            : settingsSelector.FontSize ===
                                              "Extra Large"
                                            ? "x-large"
                                            : "0.875rem"
                                        }`,
                                      }}
                                    >
                                      {t("jobs.addNewJob.shiftStartTime")}
                                    </Label>
                                    <Field
                                      type={"time"}
                                      name={`shifts_data.${index}.start_time`}
                                      className={`form-control ${
                                        index === 0 ? "" : "mt-1"
                                      }`}
                                      required
                                      style={{
                                        fontSize: `${
                                          settingsSelector.FontSize === "Large"
                                            ? "large"
                                            : settingsSelector.FontSize ===
                                              "Extra Large"
                                            ? "x-large"
                                            : "14px"
                                        }`,
                                      }}
                                    />
                                    <ErrorMessage
                                      name={`shifts_data.${index}.start_time`}
                                    >
                                      {(msg) => (
                                        <span style={{ color: "red" }}>
                                          {msg}
                                        </span>
                                      )}
                                    </ErrorMessage>
                                  </Col>
                                  <Col sm={3} md={3}>
                                    <Label
                                      style={{
                                        fontSize: `${
                                          settingsSelector.FontSize === "Large"
                                            ? "large"
                                            : settingsSelector.FontSize ===
                                              "Extra Large"
                                            ? "x-large"
                                            : "0.875rem"
                                        }`,
                                      }}
                                    >
                                      {t("jobs.addNewJob.shiftEntTime")}
                                    </Label>
                                    <Field
                                      type={"time"}
                                      name={`shifts_data.${index}.end_time`}
                                      className={`form-control ${
                                        index === 0 ? "" : "mt-1"
                                      }`}
                                      required
                                    />
                                    <ErrorMessage
                                      name={`shifts_data.${index}.end_time`}
                                    >
                                      {(msg) => (
                                        <span style={{ color: "red" }}>
                                          {msg}
                                        </span>
                                      )}
                                    </ErrorMessage>
                                  </Col>
                                  <Button
                                    type="button"
                                    className={`ml-1 ${
                                      index === 0 ? "" : "mt-1"
                                    }`}
                                    color={"primary"}
                                    onClick={() => arrayHelpers.remove(index)} // remove a location from the list
                                    style={{
                                      fontSize: `${
                                        settingsSelector.FontSize === "Large"
                                          ? "large"
                                          : settingsSelector.FontSize ===
                                            "Extra Large"
                                          ? "x-large"
                                          : "14px"
                                      }`,
                                    }}
                                  >
                                    -
                                  </Button>
                                  <Button
                                    type="button"
                                    className={`ml-1 ${
                                      index === 0 ? "" : "mt-1"
                                    }`}
                                    color={"primary"}
                                    onClick={() =>
                                      arrayHelpers.insert(index + 1, {
                                        start_time: "",
                                        end_time: "",
                                      })
                                    } // insert an empty string at a position
                                    style={{
                                      fontSize: `${
                                        settingsSelector.FontSize === "Large"
                                          ? "large"
                                          : settingsSelector.FontSize ===
                                            "Extra Large"
                                          ? "x-large"
                                          : "14px"
                                      }`,
                                    }}
                                  >
                                    +
                                  </Button>
                                </Row>
                              </div>
                            ))
                          ) : (
                            <Button
                              type="button"
                              color={"primary"}
                              onClick={() => arrayHelpers.push("")}
                              style={{
                                fontSize: `${
                                  settingsSelector.FontSize === "Large"
                                    ? "large"
                                    : settingsSelector.FontSize ===
                                      "Extra Large"
                                    ? "x-large"
                                    : "14px"
                                }`,
                              }}
                            >
                              Lisa vahetus
                            </Button>
                          )}
                        </div>
                      )}
                    />
                    <Row>
                      <Col sm={4} md={4}>
                        <FormGroup>
                          <Label
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "0.875rem"
                              }`,
                            }}
                          >
                            {t("jobs.addNewJob.recess")}
                          </Label>
                          {/*<Label>Lunch and Rest Time</Label>*/}
                          <Field
                            type={"text"}
                            className="form-control "
                            id={"recess"}
                            name={"recess"}
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                            }}
                          />
                          <ErrorMessage name={`recess`}>
                            {(msg) => (
                              <span style={{ color: "red" }}>{msg}</span>
                            )}
                          </ErrorMessage>
                        </FormGroup>
                      </Col>
                    </Row>
                    <Row>
                      <Col sm={6} md={6}>
                        <FormGroup>
                          <Label
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "0.875rem"
                              }`,
                            }}
                          >
                            {t("jobs.addNewJob.requirements")}
                          </Label>
                          {/*<Label>*/}
                          {/*  Requirements for employees' health, knowledge,*/}
                          {/*  skills, abilities, personal characteristics*/}
                          {/*</Label>*/}
                          <Field
                            as={"textarea"}
                            className="form-control "
                            id={"requirements"}
                            name={"requirements"}
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                            }}
                          />
                          <ErrorMessage name={`requirements`}>
                            {(msg) => (
                              <span style={{ color: "red" }}>{msg}</span>
                            )}
                          </ErrorMessage>
                        </FormGroup>
                        <FormGroup>
                          <Label
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "0.875rem"
                              }`,
                            }}
                          >
                            {t("jobs.addNewJob.additionalInformation")}
                          </Label>
                          {/*<Label>Comments</Label>*/}
                          <Field
                            as={"textarea"}
                            className="form-control "
                            id={"comments"}
                            name={"comments"}
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                            }}
                          />
                          <ErrorMessage name={`comments`}>
                            {(msg) => (
                              <span style={{ color: "red" }}>{msg}</span>
                            )}
                          </ErrorMessage>
                        </FormGroup>
                      </Col>
                    </Row>
                    <Row>
                      <Col sm={6} md={6}>
                        <FormGroup>
                          <Label
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "0.875rem"
                              }`,
                            }}
                          >
                            {t("jobs.addNewJob.uploadFiles")}
                          </Label>
                          <Input
                            type={"file"}
                            className="form-control"
                            id={"files"}
                            name={"file"}
                            multiple
                            onChange={(e) => handleFileChange(e)}
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                            }}
                          />
                          {fileTypeError && (
                            <Alert color={"danger"} className={"mt-2"}>
                              {t("errors.invalidFileType")}
                            </Alert>
                          )}
                          <Alert color={"info"} className={"mt-2"}>
                            <strong>{t("alerts.allowedFormats")}:</strong> docx,
                            xls, csv, zip, pdf, jpg, png
                          </Alert>
                        </FormGroup>
                        <Card className={"mt-3"}>
                          <CardBody
                            style={{ boxShadow: "5px 10px 8px #888888" }}
                            className={"p-2"}
                          >
                            {itemById.files?.length > 0
                              ? itemById.files?.map((file, index) => (
                                  <div
                                    key={index}
                                    className={
                                      "d-flex flex-row justify-content-between"
                                    }
                                    id={`id-${index}`}
                                  >
                                    {file.file_name}
                                    <div>
                                      <button
                                        type={"button"}
                                        className="btn btn-icon btn-sm js-sweetalert"
                                        onClick={() => {
                                          handleSelectedImageDivDelete(
                                            index,
                                            file.id
                                          );
                                        }}
                                        style={{
                                          fontSize: `${
                                            settingsSelector.FontSize ===
                                            "Large"
                                              ? "large"
                                              : settingsSelector.FontSize ===
                                                "Extra Large"
                                              ? "x-large"
                                              : "14px"
                                          }`,
                                        }}
                                      >
                                        <i className="fa fa-trash-o text-danger" />
                                      </button>
                                    </div>
                                  </div>
                                ))
                              : "No File Found"}
                          </CardBody>
                        </Card>
                      </Col>
                    </Row>
                    <Row>
                      <Col sm={12} md={12}>
                        <FormGroup>
                          <Button
                            type={"button"}
                            color={"primary"}
                            onClick={() => setVideoModal(true)}
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
                            {t("jobs.addNewJob.addVideoButton")}
                          </Button>
                        </FormGroup>
                      </Col>
                    </Row>
                    <Row>
                      <Col sm={6} md={6}>
                        {itemById &&
                        itemById.videos !== undefined &&
                        itemById.videos.length > 0 ? (
                          <>
                            {itemById.videos.map((item, index) => (
                              <div
                                key={index}
                                className={"d-flex justify-content-between"}
                                id={`id-${index}`}
                              >
                                <Field
                                  type={"hidden"}
                                  id={`old_files`}
                                  name={`old_files`}
                                  style={{
                                    fontSize: `${
                                      settingsSelector.FontSize === "Large"
                                        ? "large"
                                        : settingsSelector.FontSize ===
                                          "Extra Large"
                                        ? "x-large"
                                        : "14px"
                                    }`,
                                  }}
                                />
                                {item.file_name} {item.link}
                                <form>
                                  <button
                                    type="button"
                                    className="btn btn-icon btn-sm js-sweetalert"
                                    title="Delete"
                                    data-type="confirm"
                                    onClick={() => {
                                      handleSelectedDivDelete(index, item.id);
                                    }}
                                  >
                                    <i className="fa fa-trash-o text-danger" />
                                  </button>
                                </form>
                              </div>
                            ))}
                          </>
                        ) : (
                          "Ei ole ühtegi videot lisatud"
                        )}
                        {uploadVideoFiles.length > 0 && (
                          <FormGroup className={"mt-3"}>
                            <Label
                              style={{
                                fontSize: `${
                                  settingsSelector.FontSize === "Large"
                                    ? "large"
                                    : settingsSelector.FontSize ===
                                      "Extra Large"
                                    ? "x-large"
                                    : "16px"
                                }`,
                              }}
                            >
                              Uued videod
                            </Label>
                            {uploadVideoFiles.map((item) => (
                              <>
                                <p>{item.link ? item.link : ""}</p>
                                <p>{item.file ? item.file.name : ""}</p>
                              </>
                            ))}
                          </FormGroup>
                        )}
                      </Col>
                    </Row>
                  </CardBody>
                  <CardFooter className={"text-right"}>
                    <Button
                      type="submit"
                      color={"primary"}
                      className="mr-2"
                      disabled={jobsSelector.isLoading}
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
                      {jobsSelector.isLoading ? (
                        <Loader />
                      ) : (
                        <>{t("buttons.update")}</>
                      )}
                    </Button>
                    <Button
                      type="button"
                      color={"primary"}
                      onClick={() => history.push(`/hr-jobs/view/${id}`)}
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
                  </CardFooter>
                </fieldset>
              </Form>
            )}
          />
        </Card>
      </Container>
      <Toast />
    </>
  );
};

export default EditJobs;
