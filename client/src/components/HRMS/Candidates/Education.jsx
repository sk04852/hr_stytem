import React, { useEffect, useState } from "react";
import {
  Button,
  Card,
  CardBody,
  CardHeader,
  Col,
  FormGroup,
  Input,
  Label,
  Row,
} from "reactstrap";
import "./candidate.css";
import { useDispatch, useSelector } from "react-redux";
import { Formik, Form, FieldArray, Field, getIn, ErrorMessage } from "formik";
import {
  addCandidateEducation,
  deleteCandidateEducationByIds,
  getCandidateEducationById,
} from "../../../redux/actions/candidatesAction";
// import { getEducationDegree } from "../../../redux/actions/educationActions";
import { updateCandidateEducation } from "../../../redux/actions/candidatesAction";
import asterik from "../../../assets/images/asterisk.png";
import Toast from "../../constants/toast";
import { Loader } from "../../constants/loaders";
import {
  FormValidationErrors,
  SubmitFormValidationErrors,
} from "../../constants/errors";
import { useParams } from "react-router-dom";
import * as moment from "moment";
import axios from "axios";
import { baseURL } from "../../Shared/baseURL";
import { DayPicker, YearPicker } from "react-dropdown-date";
import { array, object, string } from "yup";
import { useTranslation } from "react-i18next";
import { EducationValidationSchema } from "./validations/formValidation";
import { validateHtmlTags } from "./validations/validateHtmlTags";

const Education = ({ candidateID }) => {
  const { id } = useParams();
  const initialValues = {
    education: [],
  };

  const newInitialValues = {
    education: [],
  };

  const dispatch = useDispatch();

  const { t } = useTranslation();

  const [formValues, setFormValues] = useState(initialValues);
  const [newFormValues, setNewFormValues] = useState(newInitialValues);
  const [educationById, setEducationById] = useState([]);
  const [stillWorkingChecked, setStillWorkingChecked] = useState(null);
  const [educationLevels, setEducationLevels] = useState([]);

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

  let estMonths = moment.months();

  useEffect(() => {
    const fetEducationById = async () => {
      try {
        const response = await dispatch(getCandidateEducationById(id));
        let responseData = response.payload.CandidateCVEducation;

        setEducationById(responseData);

        let candidateEducationData = {
          education: [],
        };

        for (let education in responseData) {
          let educationObj = responseData[education];
          if (education) {
            candidateEducationData.education.push({
              id: educationObj.id,
              candidatecv_id: educationObj.candidatecv_id,
              level_id:
                educationObj.level_id !== null ? educationObj.level_id : "",
              institute: educationObj.institute,
              starting_year: educationObj.starting_year,
              starting_month:
                educationObj.starting_month !== null
                  ? educationObj.starting_month
                  : "",
              starting_day:
                educationObj.starting_day !== null
                  ? educationObj.starting_day
                  : "",
              ending_year:
                educationObj.ending_year !== null
                  ? educationObj.ending_year
                  : "",
              ending_month:
                educationObj.ending_month !== null
                  ? educationObj.ending_month
                  : "",
              ending_day:
                educationObj.ending_day !== null ? educationObj.ending_day : "",
              still_studying: educationObj.still_studying,
              speciality:
                educationObj.speciality !== null ? educationObj.speciality : "",
              additonal_information:
                educationObj.additonal_information !== null
                  ? educationObj.additonal_information
                  : "",
            });
          } else {
            candidateEducationData.education = [];
            candidateEducationData.education.push({
              id: educationObj.id,
              candidatecv_id: educationObj.candidatecv_id,
              level_id:
                educationObj.level_id !== null ? educationObj.level_id : "",
              institute: educationObj.institute,
              starting_year: educationObj.starting_year,
              starting_month:
                educationObj.starting_month !== null
                  ? educationObj.starting_month
                  : "",
              starting_day:
                educationObj.starting_day !== null
                  ? educationObj.starting_day
                  : "",
              ending_year:
                educationObj.ending_year !== null
                  ? educationObj.ending_year
                  : "",
              ending_month:
                educationObj.ending_month !== null
                  ? educationObj.ending_month
                  : "",
              ending_day:
                educationObj.ending_day !== null ? educationObj.ending_day : "",
              still_studying: educationObj.still_studying,
              speciality:
                educationObj.speciality !== null ? educationObj.speciality : "",
              additonal_information:
                educationObj.additonal_information !== null
                  ? educationObj.additonal_information
                  : "",
            });
          }
          setStillWorkingChecked(educationObj.still_studying);
        }
        setFormValues(candidateEducationData);
      } catch (error) {
        throw error;
      }
    };
    fetEducationById();
  }, []);

  // useEffect(() => {
  //   const fetchEducationDegrees = async () => {
  //     try {
  //       const response = await dispatch(getEducationDegree());
  //       console.log("response=====", response);
  //     } catch (error) {
  //       throw error;
  //     }
  //   };
  //   fetchEducationDegrees();
  // }, []);

  const educationSelector = useSelector((state) => state.candidates);
  // const educationDegreesSelector = useSelector(
  //   (state) => state.educationDegree.education
  // );
  // const getCandidateEducationByIdSelector = useSelector(
  //   (state) => state.candidates.data.data.CandidateCVEducation
  // );
  const settingsSelector = useSelector((state) => state.settings);

  // useEffect(() => {
  //   if (getCandidateEducationByIdSelector) {
  //     if (educationDegreesSelector.Education_Degrees) {
  //       setEducationDegrees(educationDegreesSelector.Education_Degrees);
  //     }
  //   }
  // }, [
  //   educationDegreesSelector.Education_Degrees
  // ]);

  useEffect(() => {
    const token = localStorage.getItem("token");
    const fetchEducationLevels = async () => {
      try {
        const response = axios
          .get(`${baseURL}api/education-levels`, {
            headers: {
              Authorization: `Bearer ${token}`,
            },
          })
          .then((response) =>
            setEducationLevels(response.data.Education_Levels)
          )
          .catch((error) => console.log(error));
      } catch (error) {
        throw error;
      }
    };
    fetchEducationLevels();
  }, []);

  const handleUpdate = (values, id) => {
    // validate
    let error = {};
    values.education.map((item) => {
      error = validateHtmlTags(item, setFormErrors);
    });

    // form submit if there is no error
    if (Object.keys(error).length === 0) {
      setFormErrors({});
      let formData = new FormData();
      const submittedValues = values.education.find((items) => {
        return items.id === parseInt(id);
      });

      if (submittedValues !== undefined) {
        formData.append("id", submittedValues.id);
        formData.append("candidatecv_id", submittedValues.candidatecv_id);
        formData.append(
          "level_id",
          submittedValues.level_id === null ? "" : submittedValues.level_id
        );
        formData.append("institute", submittedValues.institute);
        formData.append(
          "speciality",
          submittedValues.speciality === null ? "" : submittedValues.speciality
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
        if (stillWorkingChecked === 0) {
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
        formData.append("still_studying", submittedValues.still_studying);
        formData.append(
          "additonal_information",
          submittedValues.additonal_information
        );

        if (submittedValues.additonal_information.length < 256) {
          dispatch(updateCandidateEducation(formData, setErrors));
        }
      }
    }
  };

  const handleDelete = (id) => {
    dispatch(deleteCandidateEducationByIds(id));
  };

  const handleSubmit = (values) => {
    // validate
    let error = {};
    values.education.map((item) => {
      error = validateHtmlTags(item, setFormErrors);
    });

    // form submit if there is no error
    if (Object.keys(error).length === 0) {
      setFormErrors({});
      const formData = new FormData();

      values.education.map((item, index) => {
        if (item.still_studying === true) {
          item.still_studying = 1;
          item.ending_year = "";
          item.ending_month = "";
          item.ending_day = "";
        } else if (item.still_studying === false) {
          item.still_studying = 0;
        }
      });

      if (values.education.length > 0) {
        for (let i = 0; i < values.education.length; i++) {
          Object.keys(values.education[i]).map((items) => {
            formData.append(
              `education[${i}][${items}]`,
              values.education[i][items]
            );
          });
        }
      }

      dispatch(addCandidateEducation(formData, setErrors, ""));
    }
  };

  const handleUpdateCheckbox = (e, index, values) => {
    let elements = document.getElementById(`education.${index}.end-year`);
    let endMonthElement = document.getElementById(
      `education.${index}.end-month`
    );
    let endDayElement = document.getElementById(`education.${index}.end-day`);
    if (e.target.checked) {
      elements.disabled = true;
      setStillWorkingChecked(1);
      values.education[index].still_studying = 1;
      values.education[index].ending_year = null;
      // setEndYear(null)
      // setEndMonth(null)
      // setEndDay(null)
    } else {
      elements.disabled = false;
      setStillWorkingChecked(0);
      values.education[index].still_studying = 0;
    }

    if (e.target.checked) {
      endMonthElement.disabled = true;
      values.education[index].ending_month = null;
    } else {
      setEndMonth(null);
    }

    if (e.target.checked) {
      endDayElement.disabled = true;
      values.education[index].ending_month = null;
    } else {
      setEndDay(null);
    }
  };

  const handleCheckbox = (e, index, values) => {
    let endYearElement = document.getElementsByName(
      `education.${index}.ending_year`
    );
    let endMonthElement = document.getElementsByName(
      `education.${index}.ending_month`
    );
    let endDayElement = document.getElementsByName(
      `education.${index}.ending_day`
    );

    let educationEndingValues = values.education[index];

    if (endYearElement.length > 0) {
      for (let i = 0; i < endYearElement.length; i++) {
        if (e.target.checked) {
          endYearElement[i].disabled = true;
          endYearElement[i].value = "";
          educationEndingValues.ending_year = "";
        } else {
          endYearElement[i].disabled = false;
        }
      }
    }
    if (endMonthElement.length > 0) {
      for (let i = 0; i < endMonthElement.length; i++) {
        if (e.target.checked) {
          endMonthElement[i].disabled = true;
          endMonthElement[i].value = "";
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
          <h1>{t("candidates.bookmarks.educationBookmark")}</h1>
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
            validationSchema={EducationValidationSchema}
            onSubmit={(values) => handleUpdate(values)}
            children={({ values, setFieldValue, error, touched }) => (
              <Form className={"w-100"}>
                <fieldset disabled={educationSelector.isLoading}>
                  <FormGroup>
                    <FieldArray
                      name={"education"}
                      render={(arrayHelpers) => (
                        <Row>
                          {values.education.map((items, index) => (
                            <div key={index} className={"w-100"}>
                              <Row>
                                <Field
                                  type={"hidden"}
                                  name={`education.${index}.candidatecv_id`}
                                  value={4}
                                />
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
                                    {/* EDUCATION LEVEL */}
                                    {t(
                                      "candidates.bookmarks.education.educationLevel"
                                    )}
                                  </Label>
                                  <Field
                                    as={"select"}
                                    id={"degree-name"}
                                    name={`education.${index}.level_id`}
                                    className={"form-control"}
                                    placeholder={"Kraad"}
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
                                    <option value={""}>
                                      Select Education Level
                                    </option>
                                    {educationLevels &&
                                      educationLevels.map((levels, index) => (
                                        <option key={index} value={levels.id}>
                                          {levels.name}
                                        </option>
                                      ))}
                                  </Field>
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
                                      "candidates.bookmarks.education.institute"
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
                                    id={"institute-name"}
                                    name={`education.${index}.institute`}
                                    className={"form-control"}
                                    placeholder={"Õppeasutus"}
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
                                  {errors.education &&
                                  errors.education[index] &&
                                  errors.education[index].institute &&
                                  touched.education &&
                                  touched.education[index] &&
                                  touched.education[index].institute ? (
                                    <div style={{ color: "red" }}>
                                      {errors.education[index].institute}
                                    </div>
                                  ) : null}
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
                                      "candidates.bookmarks.education.speciality"
                                    )}
                                  </Label>
                                  <Field
                                    type={"text"}
                                    id={"speciality"}
                                    name={`education.${index}.speciality`}
                                    className={"form-control"}
                                    placeholder={"Eriala"}
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
                                      "candidates.bookmarks.education.educationStartDate"
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
                                          values.education[index].starting_year
                                        }
                                        onChange={(year) => {
                                          setFieldValue(
                                            `education.${index}.starting_year`,
                                            year
                                          );
                                          // setStartYear(year)
                                        }}
                                        id={"year"}
                                        name={`education.${index}.starting_year`}
                                        selected={
                                          getIn(
                                            values.education.starting_year,
                                            `education.${index}.starting_year`
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
                                        name={`education.${index}.starting_month`}
                                        onChange={(month) => {
                                          handleStartMonths(month);
                                          setFieldValue(
                                            `education.${index}.starting_month`,
                                            month.target.value
                                          );
                                        }}
                                        disabled={
                                          values.education[index]
                                            .starting_year === ""
                                        }
                                        defaultValue={
                                          values.education[index].starting_month
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
                                          values.education[index]
                                            .starting_year === "" ||
                                          values.education[index]
                                            .starting_month === ""
                                        }
                                        value={
                                          values.education[index]
                                            .starting_month === ""
                                            ? (values.education[
                                                index
                                              ].starting_day = "")
                                            : values.education[index]
                                                .starting_day
                                        }
                                        onChange={(day) => {
                                          setFieldValue(
                                            `education.${index}.starting_day`,
                                            day
                                          );
                                          setStartDay(day);
                                        }}
                                        id={"day"}
                                        name={`education.${index}.starting_day`}
                                        selected={
                                          getIn(
                                            values.education.starting_day,
                                            `education.${index}.starting_day`
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
                                      "candidates.bookmarks.education.educationEndDate"
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
                                          values.education[index].ending_year
                                        }
                                        disabled={
                                          values.education[index]
                                            .still_studying === 1
                                        }
                                        onChange={(year) => {
                                          setFieldValue(
                                            `education.${index}.ending_year`,
                                            year
                                          );
                                          setEndYear(year);
                                        }}
                                        id={`education.${index}.end-year`}
                                        name={`education.${index}.ending_year`}
                                        selected={
                                          getIn(
                                            values.education.ending_year,
                                            `education.${index}.ending_year`
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
                                        name={`education.${index}.ending_month`}
                                        id={`education.${index}.end-month`}
                                        onChange={(month) => {
                                          handleEndMonths(month);
                                          setFieldValue(
                                            `education.${index}.ending_month`,
                                            month.target.value
                                          );
                                        }}
                                        disabled={
                                          values.education[index]
                                            .ending_year === ""
                                        }
                                        defaultValue={
                                          values.education[index].ending_month
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
                                          values.education[index]
                                            .ending_year === "" ||
                                          values.education[index]
                                            .ending_month === ""
                                        }
                                        value={
                                          values.education[index]
                                            .ending_year === "" ||
                                          values.education[index]
                                            .ending_month === ""
                                            ? (values.education[
                                                index
                                              ].ending_day = "")
                                            : values.education[index].ending_day
                                        }
                                        onChange={(day) => {
                                          setFieldValue(
                                            `education.${index}.ending_day`,
                                            day
                                          );
                                          setEndDay(day);
                                        }}
                                        id={`education.${index}.end-day`}
                                        name={`education.${index}.ending_day`}
                                        selected={
                                          getIn(
                                            values.education.ending_day,
                                            `education.${index}.ending_day`
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
                                        name={`education.${index}.still_studying`}
                                        id={"still-studying"}
                                        onChange={(e) =>
                                          handleUpdateCheckbox(e, index, values)
                                        }
                                        defaultChecked={
                                          values.education[index]
                                            .still_studying === 1
                                        }
                                        value={
                                          values.education[index].still_studying
                                        }
                                      />{" "}
                                      {t(
                                        "candidates.bookmarks.education.stillStudying"
                                      )}
                                    </Label>
                                  </FormGroup>
                                </Col>
                              </Row>
                              <Col sm={12} md={12}>
                                <Label>
                                  {t(
                                    "candidates.bookmarks.education.additionalInformation"
                                  )}
                                </Label>
                                <Field
                                  as={"textarea"}
                                  className={"form-control"}
                                  name={`education.${index}.additonal_information`}
                                />
                                <ErrorMessage
                                  name={`education.${index}.additonal_information`}
                                >
                                  {(msg) => (
                                    <span style={{ color: "red" }}>{msg}</span>
                                  )}
                                </ErrorMessage>
                              </Col>
                              {values.education.length > 0 && (
                                <Button
                                  type="button"
                                  color={"danger"}
                                  className={"mb-2 mt-2 ml-1"}
                                  onClick={() => {
                                    if (items.id !== null) {
                                      const confirm =
                                        window.confirm("Oled kindel?");
                                      if (confirm === true) {
                                        handleDelete(items.id);
                                        arrayHelpers.remove(index);
                                      }
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
                                    "candidates.bookmarks.education.deleteEducationHistory"
                                  )}
                                </Button>
                              )}
                              <Button
                                type={"submit"}
                                color="primary"
                                className={"mb-2 mt-2 ml-1"}
                                disabled={educationSelector.isLoading}
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
                                {educationSelector.isLoading ? (
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
            validationSchema={EducationValidationSchema}
            onSubmit={(values) => handleSubmit(values)}
            children={({ values, setFieldValue }) => (
              <Form className={"w-100"}>
                <fieldset disabled={educationSelector.isLoading}>
                  <FormGroup>
                    <FieldArray
                      name={"education"}
                      render={(arrayHelpers) => (
                        <>
                          <Row>
                            {values.education && values.education.length > 0
                              ? values.education.map((items, index) => (
                                  <div key={index} className={"w-100"}>
                                    <Row>
                                      <Field
                                        type={"hidden"}
                                        name={`education.${index}.candidatecv_id`}
                                        value={4}
                                      />
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
                                            "candidates.bookmarks.education.educationLevel"
                                          )}
                                        </Label>
                                        <Field
                                          as={"select"}
                                          id={"degree-name"}
                                          name={`education.${index}.level_id`}
                                          className={"form-control"}
                                          placeholder={"Kraad"}
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
                                          <option value={""}>
                                            Select Education Level
                                          </option>
                                          {educationLevels &&
                                            educationLevels.map(
                                              (levels, index) => (
                                                <option
                                                  key={index}
                                                  value={levels.id}
                                                >
                                                  {levels.name}
                                                </option>
                                              )
                                            )}
                                        </Field>
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
                                            "candidates.bookmarks.education.institute"
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
                                          id={"institute-name"}
                                          name={`education.${index}.institute`}
                                          className={"form-control"}
                                          placeholder={"Õppeasutus"}
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
                                            "candidates.bookmarks.education.speciality"
                                          )}
                                        </Label>
                                        <Field
                                          type={"text"}
                                          id={"speciality"}
                                          name={`education.${index}.speciality`}
                                          className={"form-control"}
                                          placeholder={"Eriala"}
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
                                            "candidates.bookmarks.education.educationStartDate"
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
                                                values.education[index]
                                                  .starting_year
                                              }
                                              onChange={(year) => {
                                                setFieldValue(
                                                  `education.${index}.starting_year`,
                                                  year
                                                );
                                                setStartYear(year);
                                              }}
                                              id={"year"}
                                              name={`education.${index}.starting_year`}
                                              selected={
                                                getIn(
                                                  values.education
                                                    .starting_year,
                                                  `education.${index}.starting_year`
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
                                              name={`education.${index}.starting_month`}
                                              onChange={(month) => {
                                                handleStartMonths(month);
                                                setFieldValue(
                                                  `education.${index}.starting_month`,
                                                  month.target.value
                                                );
                                              }}
                                              disabled={
                                                values.education[index]
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
                                                values.education[index]
                                                  .starting_year === "" ||
                                                values.education[index]
                                                  .starting_month === ""
                                              }
                                              value={
                                                values.education[index]
                                                  .starting_month === ""
                                                  ? (values.education[
                                                      index
                                                    ].starting_day = "")
                                                  : values.education[index]
                                                      .starting_day
                                              }
                                              onChange={(day) => {
                                                setFieldValue(
                                                  `education.${index}.starting_day`,
                                                  day
                                                );
                                                setStartDay(day);
                                              }}
                                              id={"day"}
                                              name={`education.${index}.starting_day`}
                                              selected={
                                                getIn(
                                                  values.education.starting_day,
                                                  `education.${index}.starting_day`
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
                                            "candidates.bookmarks.education.educationEndDate"
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
                                                values.education[index]
                                                  .ending_year
                                              }
                                              onChange={(year) => {
                                                setFieldValue(
                                                  `education.${index}.ending_year`,
                                                  year
                                                );
                                                setEndYear(year);
                                              }}
                                              id={`education.${index}.end-year`}
                                              name={`education.${index}.ending_year`}
                                              selected={
                                                getIn(
                                                  values.education.ending_year,
                                                  `education.${index}.ending_year`
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
                                              name={`education.${index}.ending_month`}
                                              onChange={(month) => {
                                                handleEndMonths(month);
                                                setFieldValue(
                                                  `education.${index}.ending_month`,
                                                  month.target.value
                                                );
                                              }}
                                              disabled={
                                                values.education[index]
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
                                                values.education[index]
                                                  .ending_year === "" ||
                                                values.education[index]
                                                  .ending_month === "" ||
                                                endYear === null ||
                                                endMonth === null
                                              }
                                              value={
                                                values.education[index]
                                                  .ending_year === "" ||
                                                values.education[index]
                                                  .ending_month === ""
                                                  ? (values.education[
                                                      index
                                                    ].ending_day = "")
                                                  : values.education[index]
                                                      .ending_day
                                              }
                                              onChange={(day) => {
                                                setFieldValue(
                                                  `education.${index}.ending_day`,
                                                  day
                                                );
                                                setEndDay(day);
                                              }}
                                              id={"day"}
                                              name={`education.${index}.ending_day`}
                                              selected={
                                                getIn(
                                                  values.education.ending_day,
                                                  `education.${index}.ending_day`
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
                                              name={`education.${index}.still_studying`}
                                              id={"still-working"}
                                              onClick={(e) =>
                                                handleCheckbox(e, index, values)
                                              }
                                            />{" "}
                                            {t(
                                              "candidates.bookmarks.education.stillStudying"
                                            )}
                                          </Label>
                                        </FormGroup>
                                      </Col>
                                    </Row>
                                    <Col sm={12} md={12}>
                                      <Label>
                                        {t(
                                          "candidates.bookmarks.education.additionalInformation"
                                        )}
                                      </Label>
                                      <Field
                                        as={"textarea"}
                                        className={"form-control"}
                                        name={`education.${index}.additonal_information`}
                                      />
                                      <ErrorMessage
                                        name={`education.${index}.additonal_information`}
                                      >
                                        {(msg) => (
                                          <span style={{ color: "red" }}>
                                            {msg}
                                          </span>
                                        )}
                                      </ErrorMessage>
                                    </Col>
                                    {values.education.length > 0 && (
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
                                          "candidates.bookmarks.education.deleteEducationHistory"
                                        )}
                                      </Button>
                                    )}
                                    <Button
                                      type={"submit"}
                                      color="primary"
                                      className={"mb-2 mt-2 ml-1"}
                                      disabled={educationSelector.isLoading}
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
                                      {educationSelector.isLoading ? (
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
                                level_id: "",
                                institute: "",
                                starting_year: "",
                                starting_month: "",
                                starting_day: "",
                                ending_year: "",
                                ending_month: "",
                                ending_day: "",
                                speciality: "",
                                still_studying: "",
                                additonal_information: "",
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
                            {t(
                              "candidates.bookmarks.education.addMoreEducationHistory"
                            )}
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

export default Education;
