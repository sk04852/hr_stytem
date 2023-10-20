import React, { useEffect, useState } from "react";
import { Field, FieldArray, Form, Formik, getIn } from "formik";
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
import { Loader } from "../../constants/loaders";
import Toast from "../../constants/toast";
import asterik from "../../../assets/images/asterisk.png";
import { useDispatch, useSelector } from "react-redux";
import {
  addAdditionalCourses,
  deleteCandidateAdditionalCourses,
  getAdditionalCoursesById,
  updateCandidateAdditionalCourses,
} from "../../../redux/actions/candidatesAction";
import {
  FormValidationErrors,
  SubmitFormValidationErrors,
} from "../../constants/errors";
import { useParams } from "react-router-dom";
import * as moment from "moment/moment";
import { DayPicker, YearPicker } from "react-dropdown-date";
import { useTranslation } from "react-i18next";
import { AdditionalCoursesValidationSchema } from "./validations/formValidation";
import { validateHtmlTags } from "./validations/validateHtmlTags";

const AdditionalCourses = ({ candidateCvId }) => {
  const { id } = useParams();
  const dispatch = useDispatch();

  const { t } = useTranslation();

  const initialValues = {
    candidatecv_id: candidateCvId,
    courses: [],
  };

  const newInitialValues = {
    courses: [],
  };

  const [formValues, setFormValues] = useState(initialValues);
  const [newFormValues, setNewFormValues] = useState(newInitialValues);

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

  const candidateSelector = useSelector((state) => state.candidates);
  const settingsSelector = useSelector((state) => state.settings);

  const fetchAdditionalCoursesById = async () => {
    try {
      const response = await dispatch(getAdditionalCoursesById(id));
      let additionalCourseData = response.payload.CandidateCVAdditionalCourses;

      let candidateAdditionalCourses = {
        candidatecv_id: candidateCvId,
        courses: [],
      };

      for (let course in additionalCourseData) {
        let additionalCourseObj = additionalCourseData[course];
        candidateAdditionalCourses.courses.push({
          id: additionalCourseObj.id,
          title: additionalCourseObj.title,
          description: additionalCourseObj.description,
          total_hours:
            additionalCourseObj.total_hours === null
              ? ""
              : additionalCourseObj.total_hours,
          starting_year: additionalCourseObj.starting_year,
          starting_month:
            additionalCourseObj.starting_month === null
              ? ""
              : additionalCourseObj.starting_month,
          starting_day:
            additionalCourseObj.starting_day === null
              ? ""
              : additionalCourseObj.starting_day,
          ending_year: additionalCourseObj.ending_year,
          ending_month:
            additionalCourseObj.ending_month === null
              ? ""
              : additionalCourseObj.ending_month,
          ending_day:
            additionalCourseObj.ending_day === null
              ? ""
              : additionalCourseObj.ending_day,
        });
        setFormValues(candidateAdditionalCourses);
      }
    } catch (error) {
      throw error;
    }
  };

  useEffect(() => {
    fetchAdditionalCoursesById();
  }, []);

  useEffect(() => {
    if (formErrors?.length > 0) {
      document.getElementById("error-messages").scrollIntoView();
    }
  }, [formErrors]);

  const handleUpdate = (values, id) => {
    // validate
    let error = {};

    values.courses.map((item) => {
      error = validateHtmlTags(item, setFormErrors);
    });

    if (Object.keys(error).length === 0) {
      setFormErrors({});
      const formData = new FormData();
      formData.append("candidatecv_id", candidateCvId);
      const submittedValues = values.courses.find((item) => {
        return item.id === id;
      });

      if (submittedValues !== undefined) {
        formData.append("id", submittedValues.id);
        formData.append(
          "title",
          submittedValues.title === null ? "" : submittedValues.title
        );
        formData.append("description", submittedValues.description);
        formData.append(
          "total_hours",
          submittedValues.total_hours === null
            ? ""
            : submittedValues.total_hours
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
        formData.append("ending_year", submittedValues.ending_year);
        formData.append(
          "ending_month",
          submittedValues.ending_month === null
            ? ""
            : submittedValues.ending_month
        );
        formData.append(
          "ending_day",
          submittedValues.ending_day === null ? "" : submittedValues.ending_day
        );

        dispatch(updateCandidateAdditionalCourses(formData, setErrors));
      }
    }
  };

  const handleSubmit = (values) => {
    // validate
    let error = {};

    values.courses.map((item) => {
      error = validateHtmlTags(item, setFormErrors);
    });

    if (Object.keys(error).length === 0) {
      setFormErrors({});
      const formData = new FormData();
      formData.append("candidatecv_id", candidateCvId);
      if (values.courses.length > 0) {
        for (let i = 0; i < values.courses.length; i++) {
          Object.keys(values.courses[i]).map((items) => {
            formData.append(
              `courses[${i}][${items}]`,
              values.courses[i][items]
            );
          });
        }
      }

      dispatch(addAdditionalCourses(formData, setErrors));
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
          <h1>{t("candidates.bookmarks.additionalCoursesBookmark")}</h1>
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
          <Row>
            <Col sm={12} md={12}>
              <Formik
                initialValues={formValues}
                enableReinitialize={true}
                validationSchema={AdditionalCoursesValidationSchema}
                onSubmit={(values) => handleUpdate(values)}
                children={({ values, setFieldValue, errors, touched }) => (
                  <Form className={"w-100"}>
                    <fieldset disabled={candidateSelector.isLoading}>
                      <FormGroup>
                        <FieldArray
                          name={"courses"}
                          render={(arrayHelpers) => (
                            <Row>
                              {values.courses.map((items, index) => (
                                <div className={"w-100"} key={index}>
                                  <Row>
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
                                          "candidates.bookmarks.additionalCourses.title"
                                        )}
                                      </Label>
                                      <Field
                                        type={"text"}
                                        id={"course-title"}
                                        name={`courses.${index}.title`}
                                        className={"form-control"}
                                        placeholder={"Nimetus"}
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
                                          "candidates.bookmarks.additionalCourses.totalHours"
                                        )}
                                      </Label>
                                      <Field
                                        type={"number"}
                                        id={`course-total-hours`}
                                        name={`courses.${index}.total_hours`}
                                        className={"form-control"}
                                        min={0}
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
                                          "candidates.bookmarks.additionalCourses.description"
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
                                        id={"course-description"}
                                        name={`courses.${index}.description`}
                                        className={"form-control"}
                                        placeholder={"Kirjeldus"}
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
                                      {errors.courses &&
                                      errors.courses[index] &&
                                      errors.courses[index].description &&
                                      touched.courses &&
                                      touched.courses[index] &&
                                      touched.courses[index].description ? (
                                        <div style={{ color: "red" }}>
                                          {errors.courses[index].description}
                                        </div>
                                      ) : null}
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
                                          "candidates.bookmarks.additionalCourses.courseStartDate"
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
                                              values.courses[index]
                                                .starting_year
                                            }
                                            onChange={(year) => {
                                              setFieldValue(
                                                `courses.${index}.starting_year`,
                                                year
                                              );
                                              setStartYear(year);
                                            }}
                                            id={"year"}
                                            name={`courses.${index}.starting_year`}
                                            selected={
                                              getIn(
                                                values.courses.starting_year,
                                                `courses.${index}.starting_year`
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
                                            name={`courses.${index}.starting_month`}
                                            onChange={(e) => {
                                              handleStartMonths(e);
                                              setFieldValue(
                                                `courses.${index}.starting_month`,
                                                e.target.value
                                              );
                                            }}
                                            disabled={
                                              values.courses[index]
                                                .starting_year === ""
                                            }
                                            defaultValue={
                                              values.courses[index]
                                                .starting_month
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
                                            defaultValue={"select day"}
                                            year={startYear}
                                            month={startMonth}
                                            endYearGiven
                                            disabled={
                                              values.courses[index]
                                                .starting_year === "" ||
                                              values.courses[index]
                                                .starting_month === ""
                                            }
                                            value={
                                              values.courses[index]
                                                .starting_month === ""
                                                ? (values.courses[
                                                    index
                                                  ].starting_day = "")
                                                : values.courses[index]
                                                    .starting_day
                                            }
                                            onChange={(day) => {
                                              setFieldValue(
                                                `courses.${index}.starting_day`,
                                                day
                                              );
                                              setStartDay(day);
                                            }}
                                            id={"day"}
                                            name={`courses.${index}.starting_day`}
                                            selected={
                                              getIn(
                                                values.courses.starting_day,
                                                `courses.${index}.starting_day`
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
                                          "candidates.bookmarks.additionalCourses.courseEndDate"
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
                                              values.courses[index].ending_year
                                            }
                                            onChange={(year) => {
                                              setFieldValue(
                                                `courses.${index}.ending_year`,
                                                year
                                              );
                                              setEndYear(year);
                                            }}
                                            id={"year"}
                                            name={`courses.${index}.ending_year`}
                                            selected={
                                              getIn(
                                                values.courses.ending_year,
                                                `courses.${index}.ending_year`
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
                                            name={`courses.${index}.ending_month`}
                                            onChange={(month) => {
                                              handleEndMonths(month);
                                              setFieldValue(
                                                `courses.${index}.ending_month`,
                                                month.target.value
                                              );
                                            }}
                                            disabled={
                                              values.courses[index]
                                                .ending_year === ""
                                            }
                                            defaultValue={
                                              values.courses[index].ending_month
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
                                              values.courses[index]
                                                .ending_year === "" ||
                                              values.courses[index]
                                                .ending_month === ""
                                            }
                                            value={
                                              values.courses[index]
                                                .ending_year === "" ||
                                              values.courses[index]
                                                .ending_month === ""
                                                ? (values.courses[
                                                    index
                                                  ].ending_day = "")
                                                : values.courses[index]
                                                    .ending_day
                                            }
                                            onChange={(day) => {
                                              setFieldValue(
                                                `courses.${index}.ending_day`,
                                                day
                                              );
                                              setEndDay(day);
                                            }}
                                            id={"day"}
                                            name={`courses.${index}.ending_day`}
                                            selected={
                                              getIn(
                                                values.courses.ending_day,
                                                `courses.${index}.ending_day`
                                              ) || ""
                                            }
                                            classes={"classes custom-select"}
                                            optionClasses={"option classes"}
                                          />
                                        </FormGroup>
                                      </div>
                                    </Col>
                                  </Row>
                                  {values.courses.length > 0 && (
                                    <Button
                                      type="button"
                                      color={"danger"}
                                      className={"mb-2 mt-2 ml-1"}
                                      onClick={() => {
                                        const confirm =
                                          window.confirm("Oled kindel?");
                                        if (confirm === true) {
                                          dispatch(
                                            deleteCandidateAdditionalCourses(
                                              items.id
                                            )
                                          );
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
                                        "candidates.bookmarks.additionalCourses.deleteCourse"
                                      )}
                                    </Button>
                                  )}
                                  <Button
                                    type={"submit"}
                                    color="primary"
                                    className={"mb-2 mt-2 ml-1"}
                                    disabled={candidateSelector.isLoading}
                                    onClick={() =>
                                      handleUpdate(values, items.id)
                                    }
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
                validationSchema={AdditionalCoursesValidationSchema}
                onSubmit={(values) => handleSubmit(values)}
                children={({ values, setFieldValue, errors, touched }) => (
                  <Form className={"w-100"}>
                    <fieldset disabled={candidateSelector.isLoading}>
                      <FormGroup>
                        <FieldArray
                          name={"courses"}
                          render={(arrayHelpers) => (
                            <>
                              <Row>
                                {values.courses && values.courses.length > 0
                                  ? values.courses.map((items, index) => (
                                      <div className={"w-100"} key={index}>
                                        <Row>
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
                                                "candidates.bookmarks.additionalCourses.title"
                                              )}
                                            </Label>
                                            <Field
                                              type={"text"}
                                              id={"course-title"}
                                              name={`courses.${index}.title`}
                                              className={"form-control"}
                                              placeholder={"Nimetus"}
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
                                                "candidates.bookmarks.additionalCourses.totalHours"
                                              )}
                                            </Label>
                                            <Field
                                              type={"number"}
                                              id={`course-total-hours`}
                                              name={`courses.${index}.total_hours`}
                                              className={"form-control"}
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
                                                "candidates.bookmarks.additionalCourses.description"
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
                                              id={"course-description"}
                                              name={`courses.${index}.description`}
                                              className={"form-control"}
                                              placeholder={"Kirjeldus"}
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
                                            {errors.courses &&
                                            errors.courses[index] &&
                                            errors.courses[index].description &&
                                            touched.courses &&
                                            touched.courses[index] &&
                                            touched.courses[index]
                                              .description ? (
                                              <div style={{ color: "red" }}>
                                                {
                                                  errors.courses[index]
                                                    .description
                                                }
                                              </div>
                                            ) : null}
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
                                                "candidates.bookmarks.additionalCourses.courseStartDate"
                                              )}
                                            </Label>
                                            <div
                                              className={
                                                "w-100 d-flex justify-content-around"
                                              }
                                            >
                                              <FormGroup>
                                                <Label>
                                                  {t(
                                                    "candidates.bookmarks.year"
                                                  )}{" "}
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
                                                    values.courses[index]
                                                      .starting_year
                                                  }
                                                  onChange={(year) => {
                                                    setFieldValue(
                                                      `courses.${index}.starting_year`,
                                                      year
                                                    );
                                                    setStartYear(year);
                                                  }}
                                                  id={"year"}
                                                  name={`courses.${index}.starting_year`}
                                                  selected={
                                                    getIn(
                                                      values.courses
                                                        .starting_year,
                                                      `courses.${index}.starting_year`
                                                    ) || ""
                                                  }
                                                  classes={
                                                    "classes custom-select"
                                                  }
                                                  optionClasses={
                                                    "option classes"
                                                  }
                                                />
                                              </FormGroup>
                                              <FormGroup>
                                                <Label>
                                                  {t(
                                                    "candidates.bookmarks.month"
                                                  )}
                                                </Label>
                                                <select
                                                  className={"custom-select"}
                                                  name={`courses.${index}.starting_month`}
                                                  onChange={(e) => {
                                                    handleStartMonths(e);
                                                    setFieldValue(
                                                      `courses.${index}.starting_month`,
                                                      e.target.value
                                                    );
                                                  }}
                                                  disabled={
                                                    values.courses[index]
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
                                                  {t(
                                                    "candidates.bookmarks.day"
                                                  )}
                                                </Label>
                                                <DayPicker
                                                  defaultValue={"select day"}
                                                  year={startYear}
                                                  month={startMonth}
                                                  endYearGiven
                                                  disabled={
                                                    values.courses[index]
                                                      .starting_year === "" ||
                                                    values.courses[index]
                                                      .starting_month === ""
                                                  }
                                                  value={
                                                    values.courses[index]
                                                      .starting_month === ""
                                                      ? (values.courses[
                                                          index
                                                        ].starting_day = "")
                                                      : values.courses[index]
                                                          .starting_day
                                                  }
                                                  onChange={(day) => {
                                                    setFieldValue(
                                                      `courses.${index}.starting_day`,
                                                      day
                                                    );
                                                    setStartDay(day);
                                                  }}
                                                  id={"day"}
                                                  name={`courses.${index}.starting_day`}
                                                  selected={
                                                    getIn(
                                                      values.courses
                                                        .starting_day,
                                                      `courses.${index}.starting_day`
                                                    ) || ""
                                                  }
                                                  classes={
                                                    "classes custom-select"
                                                  }
                                                  optionClasses={
                                                    "option classes"
                                                  }
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
                                                "candidates.bookmarks.additionalCourses.courseEndDate"
                                              )}
                                            </Label>
                                            <div
                                              className={
                                                "w-100 d-flex justify-content-around"
                                              }
                                            >
                                              <FormGroup>
                                                <Label>
                                                  {t(
                                                    "candidates.bookmarks.year"
                                                  )}{" "}
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
                                                    values.courses[index]
                                                      .ending_year
                                                  }
                                                  onChange={(year) => {
                                                    setFieldValue(
                                                      `courses.${index}.ending_year`,
                                                      year
                                                    );
                                                    setEndYear(year);
                                                  }}
                                                  id={"year"}
                                                  name={`courses.${index}.ending_year`}
                                                  selected={
                                                    getIn(
                                                      values.courses
                                                        .ending_year,
                                                      `courses.${index}.ending_year`
                                                    ) || ""
                                                  }
                                                  classes={
                                                    "classes custom-select"
                                                  }
                                                  optionClasses={
                                                    "option classes"
                                                  }
                                                />
                                              </FormGroup>
                                              <FormGroup>
                                                <Label>
                                                  {t(
                                                    "candidates.bookmarks.month"
                                                  )}
                                                </Label>
                                                <select
                                                  className={"custom-select"}
                                                  name={`courses.${index}.ending_month`}
                                                  onChange={(month) => {
                                                    handleEndMonths(month);
                                                    setFieldValue(
                                                      `courses.${index}.ending_month`,
                                                      month.target.value
                                                    );
                                                  }}
                                                  disabled={
                                                    values.courses[index]
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
                                                  {t(
                                                    "candidates.bookmarks.day"
                                                  )}
                                                </Label>
                                                <DayPicker
                                                  defaultValue={"Select Day"}
                                                  year={endYear}
                                                  month={endMonth}
                                                  endYearGiven
                                                  disabled={
                                                    values.courses[index]
                                                      .ending_year === "" ||
                                                    values.courses[index]
                                                      .ending_month === ""
                                                  }
                                                  value={
                                                    values.courses[index]
                                                      .ending_year === "" ||
                                                    values.courses[index]
                                                      .ending_month === ""
                                                      ? (values.courses[
                                                          index
                                                        ].ending_day = "")
                                                      : values.courses[index]
                                                          .ending_day
                                                  }
                                                  onChange={(day) => {
                                                    setFieldValue(
                                                      `courses.${index}.ending_day`,
                                                      day
                                                    );
                                                    setEndDay(day);
                                                  }}
                                                  id={"day"}
                                                  name={`courses.${index}.ending_day`}
                                                  selected={
                                                    getIn(
                                                      values.courses.ending_day,
                                                      `courses.${index}.ending_day`
                                                    ) || ""
                                                  }
                                                  classes={
                                                    "classes custom-select"
                                                  }
                                                  optionClasses={
                                                    "option classes"
                                                  }
                                                />
                                              </FormGroup>
                                            </div>
                                          </Col>
                                        </Row>
                                        {values.courses.length > 0 && (
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
                                              "candidates.bookmarks.additionalCourses.deleteCourse"
                                            )}
                                          </Button>
                                        )}
                                        <Button
                                          type={"submit"}
                                          color="primary"
                                          className={"mb-2 mt-2 ml-1"}
                                          disabled={candidateSelector.isLoading}
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
                                    title: "",
                                    description: "",
                                    total_hours: "",
                                    starting_year: "",
                                    starting_month: "",
                                    starting_day: "",
                                    ending_year: "",
                                    ending_month: "",
                                    ending_day: "",
                                  });
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
                                  "candidates.bookmarks.additionalCourses.addMoreCourses"
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
            </Col>
          </Row>
        </CardBody>
      </Card>
      <Toast />
    </>
  );
};

export default AdditionalCourses;
