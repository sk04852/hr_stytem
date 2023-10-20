import React, { useEffect, useState } from "react";
import {
  Button,
  Card,
  CardBody,
  CardHeader,
  Col,
  FormGroup,
  Label,
  Row,
} from "reactstrap";
import "./candidate.css";
import { Field, FieldArray, Form, Formik, getIn } from "formik";
import {
  addCandidateJobHistory,
  deleteCandidateJobHistoryByIds,
  updateCandidateJobHistory,
} from "../../../redux/actions/candidatesAction";
import { getCandidateJobHistoryById } from "../../../redux/actions/candidatesAction";
import asterik from "../../../assets/images/asterisk.png";
import { useDispatch, useSelector } from "react-redux";
import { Loader } from "../../constants/loaders";
import Toast from "../../constants/toast";
import {
  FormValidationErrors,
  SubmitFormValidationErrors,
} from "../../constants/errors";
import { useParams } from "react-router-dom";
import { DayPicker, YearPicker } from "react-dropdown-date";
import * as moment from "moment";
import { useTranslation } from "react-i18next";
import { JobHistorySchema } from "./validations/formValidation";
import { validateHtmlTags } from "./validations/validateHtmlTags";

const Information = ({ candidateID }) => {
  const { id } = useParams();
  const dispatch = useDispatch();

  const { t } = useTranslation();

  let newFormInitialValues = {
    jobs: [],
  };

  let addNewInitialValues = {
    jobs: [],
  };

  const [formValues, setFormValues] = useState(newFormInitialValues);
  const [newFormValues, setNewFormValues] = useState(addNewInitialValues);
  const [jobTypeById, setJobTypeById] = useState([]);
  const [stillWorkingChecked, setStillWorkingChecked] = useState(null);

  // ERRORS STATE
  const [errors, setErrors] = useState({});
  const [formErrors, setFormErrors] = useState({});

  // Starting and Ending Date States
  const [startYear, setStartYear] = useState(null);
  const [startMonth, setStartMonth] = useState(null);
  const [startDay, setStartDay] = useState(null);
  const [endYear, setEndYear] = useState(null);
  const [endMonth, setEndMonth] = useState(null);
  const [endDay, setEndDay] = useState(null);

  // Starting and Ending Date States for update
  // const [startYear, setStartYear] = useState(null);
  // const [startMonth, setStartMonth] = useState(null);
  // const [startDay, setStartDay] = useState(null);
  // const [endYear, setEndYear] = useState(null);
  // const [endMonth, setEndMonth] = useState(null);
  // const [endDay, setEndDay] = useState(null);

  let estMonths = moment.months();

  // const jobHistorySelector = useSelector((state) => state.candidates);
  const candidateSelector = useSelector((state) => state.candidates);
  const settingsSelector = useSelector((state) => state.settings);

  const fetchJobHistoryById = async () => {
    try {
      const response = await dispatch(getCandidateJobHistoryById(id));
      let candidateJobHistoryData = response.payload.CandidateCVJobHistories;

      setJobTypeById(candidateJobHistoryData);

      let candidateJobData = {
        jobs: [],
      };

      for (let job in candidateJobHistoryData) {
        let jobHistoryObj = candidateJobHistoryData[job];
        if (candidateJobData.hasOwnProperty("jobs")) {
          candidateJobData.jobs.push({
            id: jobHistoryObj.id,
            candidatecv_id: jobHistoryObj.candidatecv_id,
            company_name: jobHistoryObj.company_name,
            designation: jobHistoryObj.designation,
            starting_year: jobHistoryObj.starting_year,
            starting_month:
              jobHistoryObj.starting_month !== null
                ? jobHistoryObj.starting_month
                : "",
            starting_day:
              jobHistoryObj.starting_day !== null
                ? jobHistoryObj.starting_day
                : "",
            ending_year:
              jobHistoryObj.ending_year !== null
                ? jobHistoryObj.ending_year
                : "",
            ending_month:
              jobHistoryObj.ending_month !== null
                ? jobHistoryObj.ending_month
                : "",
            ending_day:
              jobHistoryObj.ending_day !== null ? jobHistoryObj.ending_day : "",
            still_working: jobHistoryObj.still_working,
            description:
              jobHistoryObj.description !== null
                ? jobHistoryObj.description
                : "",
            work_place:
              jobHistoryObj.work_place !== null ? jobHistoryObj.work_place : "",
          });
          setStillWorkingChecked(jobHistoryObj.still_working);
          // setStartYear(jobHistoryObj.starting_year)
          // setStartMonth(jobHistoryObj.starting_month)
          // setEndYear(jobHistoryObj.ending_year)
          // setEndMonth(jobHistoryObj.ending_month)
        } else {
          candidateJobData.jobs = [];
          candidateJobData.jobs.push({
            id: jobHistoryObj.id,
            candidatecv_id: jobHistoryObj.candidatecv_id,
            company_name: jobHistoryObj.company_name,
            designation: jobHistoryObj.designation,
            starting_year: jobHistoryObj.starting_year,
            starting_month:
              jobHistoryObj.starting_month !== null
                ? jobHistoryObj.starting_month
                : "",
            starting_day:
              jobHistoryObj.starting_day !== null
                ? jobHistoryObj.starting_day
                : "",
            ending_year:
              jobHistoryObj.ending_year !== null
                ? jobHistoryObj.ending_year
                : "",
            ending_month:
              jobHistoryObj.ending_month !== null
                ? jobHistoryObj.ending_month
                : "",
            ending_day:
              jobHistoryObj.ending_day !== null ? jobHistoryObj.ending_day : "",
            still_working: jobHistoryObj.still_working,
            description:
              jobHistoryObj.description !== null
                ? jobHistoryObj.description
                : "",
            work_place:
              jobHistoryObj.work_place !== null ? jobHistoryObj.work_place : "",
          });
          // setStartYear(jobHistoryObj.starting_year)
          // setStartMonth(jobHistoryObj.starting_month)
          // setEndYear(jobHistoryObj.ending_year)
          // setEndMonth(jobHistoryObj.ending_month)
        }
        setStillWorkingChecked(jobHistoryObj.still_working);
      }
      setFormValues(candidateJobData);
    } catch (error) {
      throw error;
    }
  };

  useEffect(() => {
    fetchJobHistoryById();
  }, []);

  useEffect(() => {
    if (formErrors?.length > 0) {
      document.getElementById("error-messages").scrollIntoView();
    }
  }, [formErrors]);

  const handleUpdate = (values, id) => {
    // validate
    let error = {};

    values.jobs.map((item) => {
      error = validateHtmlTags(item, setFormErrors);
    });

    // form submit if there is no error
    if (Object.keys(error).length === 0) {
      setFormErrors({});
      let formData = new FormData();

      const submittedValues = values.jobs.find((items) => {
        return items.id === parseInt(id);
      });

      if (submittedValues !== undefined) {
        formData.append("id", submittedValues.id);
        formData.append("candidatecv_id", submittedValues.candidatecv_id);
        formData.append("company_name", submittedValues.company_name);
        formData.append("designation", submittedValues.designation);
        formData.append(
          "description",
          submittedValues.description === null
            ? ""
            : submittedValues.description
        );
        formData.append(
          "work_place",
          submittedValues.work_place === null ? "" : submittedValues.work_place
        );
        formData.append("starting_year", submittedValues.starting_year);
        formData.append(
          "starting_month",
          submittedValues.starting_month === null
            ? ""
            : submittedValues.starting_month
        );
        formData.append(
          "starting_day",
          submittedValues.starting_day === null
            ? ""
            : submittedValues.starting_day
        );
        if (submittedValues.still_working === 0) {
          formData.append(
            "ending_year",
            submittedValues.ending_year === null
              ? ""
              : submittedValues.ending_year
          );
          formData.append(
            "ending_month",
            submittedValues.ending_month === null
              ? ""
              : submittedValues.ending_month
          );
          formData.append(
            "ending_day",
            submittedValues.ending_day === null
              ? ""
              : submittedValues.ending_day
          );
        } else {
          formData.append("ending_year", "");
          formData.append("ending_month", "");
          formData.append("ending_day", "");
        }
        formData.append("still_working", submittedValues.still_working);

        dispatch(updateCandidateJobHistory(formData, setErrors));
      }
    }
  };

  const handleUpdateCheckbox = (e, index, values) => {
    let elements = document.getElementById(`jobs.${index}.end-year`);
    let endMonthElement = document.getElementById(`jobs.${index}.end-month`);
    let endDayElement = document.getElementById(`jobs.${index}.end-day`);
    if (e.target.checked) {
      elements.disabled = true;
      setStillWorkingChecked(1);
      values.jobs[index].still_working = 1;
      values.jobs[index].ending_year = null;
    } else {
      elements.disabled = false;
      setStillWorkingChecked(0);
      values.jobs[index].still_working = 0;
    }

    if (e.target.checked) {
      endMonthElement.disabled = true;
      values.jobs[index].ending_month = null;
    } else {
      setEndMonth(null);
    }

    if (e.target.checked) {
      endDayElement.disabled = true;
      values.jobs[index].ending_month = null;
    } else {
      setEndDay(null);
    }
  };

  const handleDelete = (id) => {
    dispatch(deleteCandidateJobHistoryByIds(id, setErrors));
  };

  const handleSubmit = (values) => {
    // validate
    let error = {};
    values.jobs.map((item) => {
      error = validateHtmlTags(item, setFormErrors);
    });

    // form submit if there is no error
    if (Object.keys(error).length === 0) {
      setFormErrors({});
      values.jobs.map((item) => {
        if (item.still_working === true) {
          item.still_working = 1;
          item.ending_year = "";
          item.ending_month = "";
          item.ending_day = "";
        } else {
          item.still_working = 0;
        }
      });

      const formData = new FormData();
      if (values.jobs.length > 0) {
        for (let i = 0; i < values.jobs.length; i++) {
          Object.keys(values.jobs[i]).map((items) => {
            formData.append(`jobs[${i}][${items}]`, values.jobs[i][items]);
          });
        }
      }
      dispatch(addCandidateJobHistory(formData, setErrors, ""));
    }
  };

  const handleCheckbox = (e, index, values) => {
    let elements = document.getElementsByName(`jobs.${index}.ending_year`);
    let endMonthElement = document.getElementsByName(
      `jobs.${index}.ending_month`
    );
    let endDayElement = document.getElementsByName(`jobs.${index}.ending_day`);

    let jobsEndingValues = values.jobs[index];

    if (elements.length > 0) {
      for (let i = 0; i < elements.length; i++) {
        if (e.target.checked) {
          elements[i].disabled = true;
          elements[i].value = "";
          jobsEndingValues.ending_year = "";
        } else {
          elements[i].disabled = false;
        }
      }
    }
    if (endMonthElement.length > 0) {
      for (let i = 0; i < endMonthElement.length; i++) {
        if (e.target.checked) {
          endMonthElement[i].disabled = true;
          endMonthElement[i].value = "";
          // values.jobs[index].ending_month = ''
        } else {
          // endMonthElement[i].disabled = false;
          setEndMonth(null);
        }
      }
    }
    if (endDayElement.length > 0) {
      for (let i = 0; i < endDayElement.length; i++) {
        if (e.target.checked) {
          endDayElement[i].disabled = true;
          endDayElement[i].value = "";
          // values.jobs[index].ending_day = ''
        } else {
          // endDayElement[i].disabled = false;
          setEndDay(null);
        }
      }
    }
  };

  const handleStartMonths = (e) => {
    let selectedMonth = e.target.value;
    if (selectedMonth === "1") {
      setStartMonth(0);
    } else if (selectedMonth === "2") {
      setStartMonth(1);
    } else if (selectedMonth === "3") {
      setStartMonth(2);
    } else if (selectedMonth === "4") {
      setStartMonth(3);
    } else if (selectedMonth === "5") {
      setStartMonth(4);
    } else if (selectedMonth === "6") {
      setStartMonth(5);
    } else if (selectedMonth === "7") {
      setStartMonth(6);
    } else if (selectedMonth === "8") {
      setStartMonth(7);
    } else if (selectedMonth === "9") {
      setStartMonth(8);
    } else if (selectedMonth === "10") {
      setStartMonth(9);
    } else if (selectedMonth === "11") {
      setStartMonth(10);
    } else if (selectedMonth === "12") {
      setStartMonth(11);
    } else {
      setStartMonth(null);
    }
  };

  const handleEndMonths = (e) => {
    let selectedMonth = e.target.value;
    if (selectedMonth === "1") {
      setEndMonth(0);
    } else if (selectedMonth === "2") {
      setEndMonth(1);
    } else if (selectedMonth === "3") {
      setEndMonth(2);
    } else if (selectedMonth === "4") {
      setEndMonth(3);
    } else if (selectedMonth === "5") {
      setEndMonth(4);
    } else if (selectedMonth === "6") {
      setEndMonth(5);
    } else if (selectedMonth === "7") {
      setEndMonth(6);
    } else if (selectedMonth === "8") {
      setEndMonth(7);
    } else if (selectedMonth === "9") {
      setEndMonth(8);
    } else if (selectedMonth === "10") {
      setEndMonth(9);
    } else if (selectedMonth === "11") {
      setEndMonth(10);
    } else if (selectedMonth === "12") {
      setEndMonth(11);
    } else {
      setEndMonth(null);
    }
  };

  return (
    <>
      <Card>
        <CardHeader className="float-right">
          <h1>{t("candidates.bookmarks.jobHistoryBookmark")}</h1>
        </CardHeader>

        {/*DYNAMIC ERRORS*/}
        {errors && Object.keys(errors).length !== 0 ? (
          <FormValidationErrors errors={errors} />
        ) : null}

        {/*FORM INPUT ERRORS*/}
        {formErrors && Object.keys(formErrors).length !== 0 ? (
          <SubmitFormValidationErrors formErrors={formErrors} />
        ) : null}

        <CardBody>
          <Formik
            initialValues={formValues}
            enableReinitialize={true}
            validationSchema={JobHistorySchema}
            onSubmit={(values) => handleUpdate(values)}
            children={({ values, setFieldValue, errors, touched }) => (
              <Form className={"w-100"} id={"add-job-history-form"}>
                <fieldset disabled={candidateSelector.isLoading}>
                  <FormGroup>
                    <FieldArray
                      name={"jobs"}
                      render={(arrayHelpers) => (
                        <Row>
                          {values.jobs.map((items, index) => (
                            <div key={index} className={"w-100"}>
                              <Row>
                                <Field
                                  type={"hidden"}
                                  name={`jobs.${index}.candidatecv_id`}
                                  value={4}
                                />
                                <Field
                                  type={"hidden"}
                                  name={`id`}
                                  value={jobTypeById.id}
                                />
                                <Col md={3} sm={3}>
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
                                    {t(
                                      "candidates.bookmarks.jobHistory.companyName"
                                    )}{" "}
                                    <img
                                      src={asterik}
                                      height={10}
                                      width={10}
                                      className="mt-n2"
                                    />
                                  </Label>
                                  <Field
                                    type={"text"}
                                    id={"company-name"}
                                    name={`jobs.${index}.company_name`}
                                    className={"form-control"}
                                    placeholder={"Company Name"}
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
                                  {errors.jobs &&
                                  errors.jobs[index] &&
                                  errors.jobs[index].company_name &&
                                  touched.jobs &&
                                  touched.jobs[index] &&
                                  touched.jobs[index].company_name ? (
                                    <div style={{ color: "red" }}>
                                      {errors.jobs[index].company_name}
                                    </div>
                                  ) : null}
                                </Col>
                                <Col md={3} sm={3}>
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
                                    {t(
                                      "candidates.bookmarks.jobHistory.designation"
                                    )}{" "}
                                    <img
                                      src={asterik}
                                      height={10}
                                      width={10}
                                      className="mt-n2"
                                    />
                                  </Label>
                                  <Field
                                    type={"text"}
                                    id={"designation"}
                                    name={`jobs.${index}.designation`}
                                    className={"form-control"}
                                    placeholder={"Amet"}
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
                                  {errors.jobs &&
                                  errors.jobs[index] &&
                                  errors.jobs[index].designation &&
                                  touched.jobs &&
                                  touched.jobs[index] &&
                                  touched.jobs[index].designation ? (
                                    <div style={{ color: "red" }}>
                                      {errors.jobs[index].designation}
                                    </div>
                                  ) : null}
                                </Col>
                                <Col md={3} sm={3}>
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
                                    {t(
                                      "candidates.bookmarks.jobHistory.workPlace"
                                    )}
                                  </Label>
                                  <Field
                                    type={"text"}
                                    id={"work-place"}
                                    name={`jobs.${index}.work_place`}
                                    className={"form-control"}
                                    placeholder={"Place of Work"}
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
                                </Col>
                                <Col md={3} sm={3}>
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
                                    {t(
                                      "candidates.bookmarks.jobHistory.description"
                                    )}
                                  </Label>
                                  <Field
                                    as={"textarea"}
                                    id={"description"}
                                    name={`jobs.${index}.description`}
                                    className={"form-control"}
                                    // placeholder={"Description"}
                                    placeholder={"Tööülesannete kirjeldus"}
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
                                </Col>
                              </Row>
                              <Row className={"w-100 mt-2"}>
                                <Col md={4} sm={4}>
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
                                    {t(
                                      "candidates.bookmarks.jobHistory.jobStartDate"
                                    )}
                                  </Label>
                                  <div
                                    className={
                                      "w-100 d-flex justify-content-around"
                                    }
                                  >
                                    <FormGroup>
                                      <Label>
                                        {t("candidates.bookmarks.year")}{" "}
                                        <img
                                          src={asterik}
                                          height={10}
                                          width={10}
                                          className="mt-n2"
                                        />
                                      </Label>
                                      <YearPicker
                                        defaultValue={"Select Year"}
                                        reverse
                                        required={true}
                                        value={values.jobs[index].starting_year}
                                        onChange={(year) => {
                                          setFieldValue(
                                            `jobs.${index}.starting_year`,
                                            year
                                          );
                                          // setStartYear(year)
                                        }}
                                        id={"year"}
                                        name={`jobs.${index}.starting_year`}
                                        selected={
                                          getIn(
                                            values.jobs.starting_year,
                                            `jobs.${index}.starting_year`
                                          ) || ""
                                        }
                                        classes={"classes custom-select"}
                                        optionClasses={"option classes"}
                                      />
                                    </FormGroup>
                                    <FormGroup>
                                      <Label>
                                        {t("candidates.bookmarks.month")}
                                      </Label>
                                      <select
                                        className={"custom-select"}
                                        name={`jobs.${index}.starting_month`}
                                        onChange={(month) => {
                                          handleStartMonths(month);
                                          setFieldValue(
                                            `jobs.${index}.starting_month`,
                                            month.target.value
                                          );
                                        }}
                                        disabled={
                                          values.jobs[index].starting_year ===
                                          ""
                                        }
                                        defaultValue={
                                          values.jobs[index].starting_month
                                        }
                                      >
                                        <option value={""}>Select Month</option>
                                        {estMonths.map((monthItem, index) => (
                                          <option key={index} value={index + 1}>
                                            {monthItem}
                                          </option>
                                        ))}
                                      </select>
                                    </FormGroup>
                                    <FormGroup>
                                      <Label>
                                        {t("candidates.bookmarks.day")}
                                      </Label>
                                      <DayPicker
                                        defaultValue={"Select Day"}
                                        year={startYear}
                                        month={startMonth}
                                        endYearGiven
                                        disabled={
                                          values.jobs[index].starting_year ===
                                            "" ||
                                          values.jobs[index].starting_month ===
                                            null
                                        }
                                        value={
                                          values.jobs[index].starting_month ===
                                          ""
                                            ? (values.jobs[index].starting_day =
                                                "")
                                            : values.jobs[index].starting_day
                                        }
                                        onChange={(day) => {
                                          setFieldValue(
                                            `jobs.${index}.starting_day`,
                                            day
                                          );
                                          setStartDay(day);
                                        }}
                                        id={"day"}
                                        name={`jobs.${index}.starting_day`}
                                        selected={
                                          getIn(
                                            values.jobs.starting_day,
                                            `jobs.${index}.starting_day`
                                          ) || ""
                                        }
                                        classes={"classes custom-select"}
                                        optionClasses={"option classes"}
                                      />
                                    </FormGroup>
                                  </div>
                                </Col>
                                <Col md={4} sm={4}>
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
                                    {t(
                                      "candidates.bookmarks.jobHistory.jobEndDate"
                                    )}
                                  </Label>
                                  <div
                                    className={
                                      "w-100 d-flex justify-content-around"
                                    }
                                  >
                                    <FormGroup>
                                      <Label>
                                        {t("candidates.bookmarks.year")}{" "}
                                        <img
                                          src={asterik}
                                          height={10}
                                          width={10}
                                          className="mt-n2"
                                        />
                                      </Label>
                                      <YearPicker
                                        defaultValue={"Select Year"}
                                        end={2033}
                                        reverse
                                        required={true}
                                        value={values.jobs[index].ending_year}
                                        disabled={
                                          values.jobs[index].still_working === 1
                                        }
                                        onChange={(year) => {
                                          setFieldValue(
                                            `jobs.${index}.ending_year`,
                                            year
                                          );
                                          setEndYear(year);
                                        }}
                                        id={`jobs.${index}.end-year`}
                                        name={`jobs.${index}.ending_year`}
                                        selected={
                                          getIn(
                                            values.jobs.ending_year,
                                            `jobs.${index}.ending_year`
                                          ) || ""
                                        }
                                        classes={"classes custom-select"}
                                        optionClasses={"option classes"}
                                      />
                                    </FormGroup>
                                    <FormGroup>
                                      <Label>
                                        {t("candidates.bookmarks.month")}
                                      </Label>
                                      <select
                                        className={"custom-select"}
                                        name={`jobs.${index}.ending_month`}
                                        id={`jobs.${index}.end-month`}
                                        onChange={(month) => {
                                          handleEndMonths(month);
                                          setFieldValue(
                                            `jobs.${index}.ending_month`,
                                            month.target.value
                                          );
                                        }}
                                        disabled={
                                          values.jobs[index].ending_year ===
                                          null
                                        }
                                        defaultValue={
                                          values.jobs[index].ending_month
                                        }
                                      >
                                        <option value={""}>Select Month</option>
                                        {estMonths.map((monthItem, index) => (
                                          <option key={index} value={index + 1}>
                                            {monthItem}
                                          </option>
                                        ))}
                                      </select>
                                    </FormGroup>
                                    <FormGroup>
                                      <Label>
                                        {t("candidates.bookmarks.day")}
                                      </Label>
                                      <DayPicker
                                        defaultValue={"Select Day"}
                                        year={endYear}
                                        month={endMonth}
                                        endYearGiven
                                        disabled={
                                          values.jobs[index].ending_year ===
                                            null ||
                                          values.jobs[index].ending_month ===
                                            null
                                        }
                                        value={
                                          values.jobs[index].ending_year ===
                                            "" ||
                                          values.jobs[index].ending_month === ""
                                            ? (values.jobs[index].ending_day =
                                                "")
                                            : values.jobs[index].ending_day
                                        }
                                        onChange={(day) => {
                                          setFieldValue(
                                            `jobs.${index}.ending_day`,
                                            day
                                          );
                                          setEndDay(day);
                                        }}
                                        id={`jobs.${index}.end-day`}
                                        name={`jobs.${index}.ending_day`}
                                        selected={
                                          getIn(
                                            values.jobs.ending_day,
                                            `jobs.${index}.ending_day`
                                          ) || ""
                                        }
                                        classes={"classes custom-select"}
                                        optionClasses={"option classes"}
                                      />
                                    </FormGroup>
                                  </div>
                                  <FormGroup>
                                    <Label
                                      className="ml-3 mt-2"
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
                                      <input
                                        type="checkbox"
                                        name={`jobs.${index}.still_working`}
                                        id={"update-still-working"}
                                        onChange={(e) =>
                                          handleUpdateCheckbox(e, index, values)
                                        }
                                        defaultChecked={
                                          values.jobs[index].still_working === 1
                                        }
                                        defaultValue={
                                          values.jobs[index].still_working
                                        }
                                      />{" "}
                                      {t(
                                        "candidates.bookmarks.jobHistory.stillWorking"
                                      )}
                                    </Label>
                                  </FormGroup>
                                </Col>
                              </Row>
                              {values.jobs.length > 0 && (
                                <Button
                                  type="button"
                                  color={"danger"}
                                  className={"mb-2 mt-2 ml-1"}
                                  onClick={() => {
                                    const confirm =
                                      window.confirm("Oled kindel?");
                                    if (confirm === true) {
                                      handleDelete(items.id);
                                      arrayHelpers.remove(index);
                                    }
                                  }}
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
                                  {t(
                                    "candidates.bookmarks.jobHistory.deleteJob"
                                  )}
                                </Button>
                              )}
                              <Button
                                type="submit"
                                color={"primary"}
                                className={"mb-2 mt-2 ml-1"}
                                disabled={candidateSelector.isLoading}
                                onClick={() => handleUpdate(values, items.id)}
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
                                {candidateSelector.isLoading ? (
                                  <Loader />
                                ) : (
                                  <>{t("buttons.update")}</>
                                )}
                              </Button>
                            </div>
                          ))}
                        </Row>
                      )}
                    />
                  </FormGroup>
                </fieldset>
              </Form>
            )}
          />
          <Formik
            initialValues={newFormValues}
            enableReinitialize={true}
            validationSchema={JobHistorySchema}
            onSubmit={(values) => handleSubmit(values)}
            children={({ values, setFieldValue, errors, touched }) => (
              <Form className={"w-100"}>
                <fieldset disabled={candidateSelector.isLoading}>
                  <FormGroup>
                    <FieldArray
                      name={"jobs"}
                      render={(arrayHelpers) => (
                        <>
                          <Row>
                            {values.jobs && values.jobs.length > 0
                              ? values.jobs.map((items, index) => (
                                  <div key={index} className={"w-100"}>
                                    <Row>
                                      <Field
                                        type={"hidden"}
                                        name={`jobs.${index}.candidatecv_id`}
                                        value={4}
                                      />
                                      <Col md={3} sm={3}>
                                        <Label
                                          style={{
                                            fontSize: `${
                                              settingsSelector.FontSize ===
                                              "Large"
                                                ? "large"
                                                : settingsSelector.FontSize ===
                                                  "Extra Large"
                                                ? "x-large"
                                                : "0.875rem"
                                            }`,
                                          }}
                                        >
                                          {t(
                                            "candidates.bookmarks.jobHistory.companyName"
                                          )}{" "}
                                          <img
                                            src={asterik}
                                            height={10}
                                            width={10}
                                            className="mt-n2"
                                          />
                                        </Label>
                                        <Field
                                          type={"text"}
                                          id={"company-name"}
                                          name={`jobs.${index}.company_name`}
                                          className={"form-control"}
                                          placeholder={"Ettevõtte nimi"}
                                          required
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
                                        />
                                        {errors.jobs &&
                                        errors.jobs[index] &&
                                        errors.jobs[index].company_name &&
                                        touched.jobs &&
                                        touched.jobs[index] &&
                                        touched.jobs[index].company_name ? (
                                          <div style={{ color: "red" }}>
                                            {errors.jobs[index].company_name}
                                          </div>
                                        ) : null}
                                      </Col>
                                      <Col md={3} sm={3}>
                                        <Label
                                          style={{
                                            fontSize: `${
                                              settingsSelector.FontSize ===
                                              "Large"
                                                ? "large"
                                                : settingsSelector.FontSize ===
                                                  "Extra Large"
                                                ? "x-large"
                                                : "0.875rem"
                                            }`,
                                          }}
                                        >
                                          {t(
                                            "candidates.bookmarks.jobHistory.designation"
                                          )}{" "}
                                          <img
                                            src={asterik}
                                            height={10}
                                            width={10}
                                            className="mt-n2"
                                          />
                                        </Label>
                                        <Field
                                          type={"text"}
                                          id={"designation"}
                                          name={`jobs.${index}.designation`}
                                          className={"form-control"}
                                          placeholder={"Amet"}
                                          required
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
                                        />
                                        {errors.jobs &&
                                        errors.jobs[index] &&
                                        errors.jobs[index].designation &&
                                        touched.jobs &&
                                        touched.jobs[index] &&
                                        touched.jobs[index].designation ? (
                                          <div style={{ color: "red" }}>
                                            {errors.jobs[index].designation}
                                          </div>
                                        ) : null}
                                      </Col>
                                      <Col md={3} sm={3}>
                                        <Label
                                          style={{
                                            fontSize: `${
                                              settingsSelector.FontSize ===
                                              "Large"
                                                ? "large"
                                                : settingsSelector.FontSize ===
                                                  "Extra Large"
                                                ? "x-large"
                                                : "0.875rem"
                                            }`,
                                          }}
                                        >
                                          {t(
                                            "candidates.bookmarks.jobHistory.workPlace"
                                          )}
                                        </Label>
                                        <Field
                                          type={"text"}
                                          id={"work-place"}
                                          name={`jobs.${index}.work_place`}
                                          className={"form-control"}
                                          placeholder={"Töö asukoht"}
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
                                        />
                                      </Col>
                                      <Col md={3} sm={3}>
                                        <Label
                                          style={{
                                            fontSize: `${
                                              settingsSelector.FontSize ===
                                              "Large"
                                                ? "large"
                                                : settingsSelector.FontSize ===
                                                  "Extra Large"
                                                ? "x-large"
                                                : "0.875rem"
                                            }`,
                                          }}
                                        >
                                          {t(
                                            "candidates.bookmarks.jobHistory.description"
                                          )}
                                        </Label>
                                        <Field
                                          as={"textarea"}
                                          id={"description"}
                                          name={`jobs.${index}.description`}
                                          className={"form-control"}
                                          placeholder={
                                            "Tööülesannete kirjeldus"
                                          }
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
                                        />
                                      </Col>
                                    </Row>
                                    <Row className={"w-100 mt-2"}>
                                      <Col md={4} sm={4}>
                                        <Label
                                          style={{
                                            fontSize: `${
                                              settingsSelector.FontSize ===
                                              "Large"
                                                ? "large"
                                                : settingsSelector.FontSize ===
                                                  "Extra Large"
                                                ? "x-large"
                                                : "0.875rem"
                                            }`,
                                          }}
                                        >
                                          {t(
                                            "candidates.bookmarks.jobHistory.jobStartDate"
                                          )}
                                        </Label>
                                        <div
                                          className={
                                            "w-100 d-flex justify-content-around"
                                          }
                                        >
                                          <FormGroup>
                                            <Label>
                                              {t("candidates.bookmarks.year")}{" "}
                                              <img
                                                src={asterik}
                                                height={10}
                                                width={10}
                                                className="mt-n2"
                                              />
                                            </Label>
                                            <YearPicker
                                              defaultValue={"Select Year"}
                                              reverse
                                              required={true}
                                              value={
                                                values.jobs[index].starting_year
                                              }
                                              onChange={(year) => {
                                                setFieldValue(
                                                  `jobs.${index}.starting_year`,
                                                  year
                                                );
                                                setStartYear(year);
                                              }}
                                              id={"year"}
                                              name={`jobs.${index}.starting_year`}
                                              selected={
                                                getIn(
                                                  values.jobs.starting_year,
                                                  `jobs.${index}.starting_year`
                                                ) || ""
                                              }
                                              classes={"classes custom-select"}
                                              optionClasses={"option classes"}
                                            />
                                          </FormGroup>
                                          <FormGroup>
                                            <Label>
                                              {t("candidates.bookmarks.month")}
                                            </Label>
                                            <select
                                              className={"custom-select"}
                                              name={`jobs.${index}.starting_month`}
                                              onChange={(month) => {
                                                handleStartMonths(month);
                                                setFieldValue(
                                                  `jobs.${index}.starting_month`,
                                                  month.target.value
                                                );
                                              }}
                                              disabled={
                                                values.jobs[index]
                                                  .starting_year === ""
                                              }
                                            >
                                              <option value={""}>
                                                Select Month
                                              </option>
                                              {estMonths.map(
                                                (monthItem, index) => (
                                                  <option
                                                    key={index}
                                                    value={index + 1}
                                                  >
                                                    {monthItem}
                                                  </option>
                                                )
                                              )}
                                            </select>
                                          </FormGroup>
                                          <FormGroup>
                                            <Label>
                                              {t("candidates.bookmarks.day")}
                                            </Label>
                                            <DayPicker
                                              defaultValue={"Select Day"}
                                              year={startYear}
                                              month={startMonth}
                                              endYearGiven
                                              disabled={
                                                values.jobs[index]
                                                  .starting_year === "" ||
                                                values.jobs[index]
                                                  .starting_month === ""
                                              }
                                              value={
                                                values.jobs[index]
                                                  .starting_month === ""
                                                  ? (values.jobs[
                                                      index
                                                    ].starting_day = "")
                                                  : values.jobs[index]
                                                      .starting_day
                                              }
                                              onChange={(day) => {
                                                setFieldValue(
                                                  `jobs.${index}.starting_day`,
                                                  day
                                                );
                                                setStartDay(day);
                                              }}
                                              id={"day"}
                                              name={`jobs.${index}.starting_day`}
                                              selected={
                                                getIn(
                                                  values.jobs.starting_day,
                                                  `jobs.${index}.starting_day`
                                                ) || ""
                                              }
                                              classes={"classes custom-select"}
                                              optionClasses={"option classes"}
                                            />
                                          </FormGroup>
                                        </div>
                                      </Col>
                                      <Col md={4} sm={4}>
                                        <Label
                                          style={{
                                            fontSize: `${
                                              settingsSelector.FontSize ===
                                              "Large"
                                                ? "large"
                                                : settingsSelector.FontSize ===
                                                  "Extra Large"
                                                ? "x-large"
                                                : "0.875rem"
                                            }`,
                                          }}
                                        >
                                          {t(
                                            "candidates.bookmarks.jobHistory.jobEndDate"
                                          )}
                                        </Label>
                                        <div
                                          className={
                                            "w-100 d-flex justify-content-around"
                                          }
                                        >
                                          <FormGroup>
                                            <Label>
                                              {t("candidates.bookmarks.year")}{" "}
                                              <img
                                                src={asterik}
                                                height={10}
                                                width={10}
                                                className="mt-n2"
                                              />
                                            </Label>
                                            <YearPicker
                                              defaultValue={"Select Year"}
                                              end={2033}
                                              reverse
                                              required={true}
                                              value={
                                                values.jobs[index].ending_year
                                              }
                                              onChange={(year) => {
                                                setFieldValue(
                                                  `jobs.${index}.ending_year`,
                                                  year
                                                );
                                                setEndYear(year);
                                              }}
                                              id={`jobs.${index}.ending_year`}
                                              name={`jobs.${index}.ending_year`}
                                              selected={
                                                getIn(
                                                  values.jobs.ending_year,
                                                  `jobs.${index}.ending_year`
                                                ) || ""
                                              }
                                              classes={"classes custom-select"}
                                              optionClasses={"option classes"}
                                            />
                                          </FormGroup>
                                          <FormGroup>
                                            <Label>
                                              {t("candidates.bookmarks.month")}
                                            </Label>
                                            <select
                                              className={"custom-select"}
                                              name={`jobs.${index}.ending_month`}
                                              id={`jobs.${index}.ending_month`}
                                              onChange={(month) => {
                                                handleEndMonths(month);
                                                setFieldValue(
                                                  `jobs.${index}.ending_month`,
                                                  month.target.value
                                                );
                                              }}
                                              disabled={
                                                values.jobs[index]
                                                  .ending_year === "" ||
                                                endYear === null
                                              }
                                            >
                                              <option value={""}>
                                                Select Month
                                              </option>
                                              {estMonths.map(
                                                (monthItem, index) => (
                                                  <option
                                                    key={index}
                                                    value={index + 1}
                                                  >
                                                    {monthItem}
                                                  </option>
                                                )
                                              )}
                                            </select>
                                          </FormGroup>
                                          <FormGroup>
                                            <Label>
                                              {t("candidates.bookmarks.day")}
                                            </Label>
                                            <DayPicker
                                              defaultValue={"Select Day"}
                                              year={endYear}
                                              month={endMonth}
                                              endYearGiven
                                              disabled={
                                                values.jobs[index]
                                                  .ending_year === "" ||
                                                values.jobs[index]
                                                  .ending_month === "" ||
                                                endYear === null ||
                                                endMonth === null
                                              }
                                              value={
                                                values.jobs[index]
                                                  .ending_year === "" ||
                                                values.jobs[index]
                                                  .ending_month === ""
                                                  ? (values.jobs[
                                                      index
                                                    ].ending_day = "")
                                                  : values.jobs[index]
                                                      .ending_day
                                              }
                                              onChange={(day) => {
                                                setFieldValue(
                                                  `jobs.${index}.ending_day`,
                                                  day
                                                );
                                                setEndDay(day);
                                              }}
                                              id={`jobs.${index}.ending_day`}
                                              name={`jobs.${index}.ending_day`}
                                              selected={
                                                getIn(
                                                  values.jobs.ending_day,
                                                  `jobs.${index}.ending_day`
                                                ) || ""
                                              }
                                              classes={"classes custom-select"}
                                              optionClasses={"option classes"}
                                            />
                                          </FormGroup>
                                        </div>
                                        <FormGroup>
                                          <Label
                                            className="ml-3 mt-2"
                                            style={{
                                              fontSize: `${
                                                settingsSelector.FontSize ===
                                                "Large"
                                                  ? "large"
                                                  : settingsSelector.FontSize ===
                                                    "Extra Large"
                                                  ? "x-large"
                                                  : "0.875rem"
                                              }`,
                                            }}
                                          >
                                            <Field
                                              type="checkbox"
                                              name={`jobs.${index}.still_working`}
                                              id={"still-working"}
                                              onClick={(e) =>
                                                handleCheckbox(e, index, values)
                                              }
                                            />{" "}
                                            {t(
                                              "candidates.bookmarks.jobHistory.stillWorking"
                                            )}
                                          </Label>
                                        </FormGroup>
                                      </Col>
                                    </Row>
                                    {values.jobs.length > 0 && (
                                      <Button
                                        type="button"
                                        color={"danger"}
                                        className={"mb-2 mt-2 ml-1"}
                                        onClick={() =>
                                          arrayHelpers.remove(index)
                                        }
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
                                        {t(
                                          "candidates.bookmarks.jobHistory.deleteJob"
                                        )}
                                      </Button>
                                    )}
                                    <Button
                                      type="submit"
                                      color={"primary"}
                                      className={"mb-2 mt-2 ml-1"}
                                      disabled={candidateSelector.isLoading}
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
                                      {candidateSelector.isLoading ? (
                                        <Loader />
                                      ) : (
                                        <>{t("buttons.update")}</>
                                      )}
                                    </Button>
                                  </div>
                                ))
                              : ""}
                          </Row>
                          <Button
                            type="button"
                            color={"primary"}
                            className={`mb-2 mt-2`}
                            onClick={() => {
                              arrayHelpers.push({
                                candidatecv_id: candidateID,
                                company_name: "",
                                designation: "",
                                starting_year: "",
                                starting_month: "",
                                starting_day: "",
                                ending_year: "",
                                ending_month: "",
                                ending_day: "",
                                still_working: "",
                                description: "",
                                work_place: "",
                              });
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
                            {t("candidates.bookmarks.jobHistory.addMoreJobs")}
                          </Button>
                        </>
                      )}
                    />
                  </FormGroup>
                </fieldset>
              </Form>
            )}
          />
        </CardBody>
      </Card>
      <Toast />
    </>
  );
};

export default Information;
