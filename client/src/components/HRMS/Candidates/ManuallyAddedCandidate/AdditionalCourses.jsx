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
import { Loader } from "../../../constants/loaders";
import Toast from "../../../constants/toast";
import asterik from "../../../../assets/images/asterisk.png";
import { addAdditionalCourses } from "../../../../redux/actions/candidatesAction";
import { useDispatch, useSelector } from "react-redux";
import {
  FormValidationErrors,
  SubmitFormValidationErrors,
} from "../../../constants/errors";
import { DayPicker, YearPicker } from "react-dropdown-date";
import * as moment from "moment";
// import { defaultLayoutPlugin } from "@react-pdf-viewer/default-layout";
// import { Viewer, Worker } from "@react-pdf-viewer/core";
import { AiOutlineLeft, AiOutlineRight } from "react-icons/ai";
import { useTranslation } from "react-i18next";
import {
  highlightFieldColor,
  highlightClickedFieldColor,
} from "../../../Shared/Colors";
import PdfViewer from "./PdfViewer";
import { AdditionalCoursesValidationSchema } from "../validations/formValidation";
import { validateHtmlTags } from "../validations/validateHtmlTags";

// funcations
import handleCandidateAdditionalCoursesCvData from "./functions/candidateCvDataFunctions/additionalCoursesCvData";
import {
  handleClickedSearchKeywords,
  handleClickedBlurSearchKeywords,
} from "./functions/handleClickedSearchKeywords";
import { handleStartMonths, handleEndMonths } from "./functions/handleMonths";

const AdditionalCourses = ({
  candidateCvID,
  cvData,
  viewPdfFile,
  setTabsId,
  cvFile,
}) => {
  const dispatch = useDispatch();

  const { t } = useTranslation();

  const initialValues = {
    candidatecv_id: candidateCvID,
    courses: [
      {
        title: "",
        description: "",
        total_hours: "",
        starting_year: "",
        starting_month: "",
        starting_day: "",
        ending_year: "",
        ending_month: "",
        ending_day: "",
      },
    ],
  };

  let specialCharacters = /[&\/\\#,+()$~%.'":*?<>{}]/g;

  const [formValues, setFormValues] = useState(initialValues);

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
  const [candidateImportFromFileUI, setCandidateImportFromFileUI] =
    useState(false);

  const [searchKeywords, setSearchKeywords] = useState([]);
  const [clickedSearchKeywords, setClickedSearchKeywords] = useState([]);

  let estMonths = moment.months();
  // const defaultLayoutPluginInstance = defaultLayoutPlugin();

  const candidateSelector = useSelector((state) => state.candidates);
  const settingsSelector = useSelector((state) => state.settings);

  useEffect(() => {
    if (cvData) {
      handleCandidateAdditionalCoursesCvData(
        cvData,
        setFormValues,
        setSearchKeywords
      );
    }
  }, []);

  useEffect(() => {
    if (formErrors?.length > 0) {
      document.getElementById("error-messages").scrollIntoView();
    }
  }, [formErrors]);

  const handleSubmit = (values) => {
    // validate
    let error = {};

    values.courses.map((item) => {
      error = validateHtmlTags(item, setFormErrors);
    });

    if (Object.keys(error).length === 0) {
      setFormErrors({});
      const formData = new FormData();
      formData.append("candidatecv_id", candidateCvID);
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

      dispatch(addAdditionalCourses(formData, setErrors, setTabsId));
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

        {viewPdfFile ? (
          <Row>
            <Col sm={12} md={12} className={"w-100 d-flex justify-content-end"}>
              <Button
                type={"button"}
                className={""}
                color={"primary"}
                onClick={() =>
                  setCandidateImportFromFileUI(!candidateImportFromFileUI)
                }
              >
                {candidateImportFromFileUI ? (
                  <>
                    <AiOutlineRight /> {t("candidates.bookmarks.hideCV")}
                  </>
                ) : (
                  <>
                    <AiOutlineLeft /> {t("candidates.bookmarks.showCV")}
                  </>
                )}
              </Button>
            </Col>
          </Row>
        ) : (
          ""
        )}

        <CardBody>
          <Row>
            <Col sm={12} md={12}>
              <Formik
                initialValues={formValues}
                enableReinitialize={true}
                validationSchema={AdditionalCoursesValidationSchema}
                onSubmit={(values) => handleSubmit(values)}
                children={({ values, setFieldValue, errors, touched }) => (
                  <Form className={"w-100"} id={"add-additional-courses-form"}>
                    <fieldset disabled={candidateSelector.isLoading}>
                      <FormGroup>
                        <FieldArray
                          name={"courses"}
                          render={(arrayHelpers) => (
                            <>
                              <Row>
                                <Col md={candidateImportFromFileUI ? 6 : 12}>
                                  {values.courses.map((items, index) => (
                                    <>
                                      <Row key={index}>
                                        <Col
                                          md={candidateImportFromFileUI ? 6 : 4}
                                          sm={4}
                                        >
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
                                            id={`course-title.${index}`}
                                            name={`courses.${index}.title`}
                                            className={"form-control"}
                                            placeholder={"Nimetus"}
                                            onClick={(e) =>
                                              handleClickedSearchKeywords(
                                                e,
                                                cvData,
                                                setClickedSearchKeywords
                                              )
                                            }
                                            onBlur={(e) =>
                                              handleClickedBlurSearchKeywords(
                                                e,
                                                cvData,
                                                setClickedSearchKeywords
                                              )
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
                                              background: `${
                                                formValues.courses &&
                                                formValues.courses?.length >
                                                  0 &&
                                                formValues.courses[index]
                                                  ?.title &&
                                                formValues.courses[index]
                                                  ?.title[index]
                                                  ? highlightFieldColor
                                                  : ""
                                              }`,
                                            }}
                                          />
                                        </Col>
                                        <Col
                                          md={candidateImportFromFileUI ? 6 : 4}
                                          sm={4}
                                        >
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
                                        <Col
                                          md={
                                            candidateImportFromFileUI ? 12 : 4
                                          }
                                          sm={4}
                                        >
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
                                            id={`course-description.${index}`}
                                            name={`courses.${index}.description`}
                                            className={"form-control"}
                                            placeholder={"Kirjeldus"}
                                            onClick={(e) =>
                                              handleClickedSearchKeywords(
                                                e,
                                                cvData,
                                                setClickedSearchKeywords
                                              )
                                            }
                                            onBlur={(e) =>
                                              handleClickedBlurSearchKeywords(
                                                e,
                                                cvData,
                                                setClickedSearchKeywords
                                              )
                                            }
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
                                              background: `${
                                                formValues.courses &&
                                                formValues.courses?.length >
                                                  0 &&
                                                formValues.courses[index]
                                                  ?.description &&
                                                formValues.courses[index]
                                                  ?.description[index]
                                                  ? highlightFieldColor
                                                  : ""
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
                                              {
                                                errors.courses[index]
                                                  .description
                                              }
                                            </div>
                                          ) : null}
                                        </Col>
                                      </Row>
                                      <Row
                                        className={`w-100 mt-2 ${
                                          candidateImportFromFileUI
                                            ? "flex-column"
                                            : "flex-row"
                                        }`}
                                      >
                                        <Col
                                          md={
                                            candidateImportFromFileUI ? 12 : 4
                                          }
                                          sm={4}
                                        >
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
                                                    values.courses
                                                      .starting_year,
                                                    `courses.${index}.starting_year`
                                                  ) || ""
                                                }
                                                classes={
                                                  "classes custom-select"
                                                }
                                                optionClasses={"option classes"}
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
                                                  handleStartMonths(
                                                    e,
                                                    setStartMonth
                                                  );
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
                                                classes={
                                                  "classes custom-select"
                                                }
                                                optionClasses={"option classes"}
                                              />
                                            </FormGroup>
                                          </div>
                                        </Col>
                                        <Col
                                          md={
                                            candidateImportFromFileUI ? 12 : 4
                                          }
                                          sm={4}
                                        >
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
                                                    values.courses.ending_year,
                                                    `courses.${index}.ending_year`
                                                  ) || ""
                                                }
                                                classes={
                                                  "classes custom-select"
                                                }
                                                optionClasses={"option classes"}
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
                                                  handleEndMonths(
                                                    month,
                                                    setEndMonth
                                                  );
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
                                                classes={
                                                  "classes custom-select"
                                                }
                                                optionClasses={"option classes"}
                                              />
                                            </FormGroup>
                                          </div>
                                        </Col>
                                      </Row>
                                      {values.courses.length > 1 && (
                                        <Button
                                          type="button"
                                          color={"danger"}
                                          className={`mb-2 mt-2 ml-1`}
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
                                    </>
                                  ))}
                                </Col>
                                <Col md={candidateImportFromFileUI ? 6 : 12}>
                                  {candidateImportFromFileUI ? (
                                    <>
                                      {viewPdfFile ? (
                                        <PdfViewer
                                          cvFile={cvFile}
                                          cvData={cvData}
                                          searchKeywords={searchKeywords}
                                          clickedSearchKeywords={
                                            clickedSearchKeywords
                                          }
                                        />
                                      ) : null}
                                    </>
                                  ) : null}
                                </Col>
                              </Row>
                              <Button
                                type="button"
                                color={"primary"}
                                className={`mb-2 mt-2 ${
                                  candidateImportFromFileUI &&
                                  values.courses.length === 1
                                    ? "position-absolute"
                                    : ""
                                }`}
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
                                  top: `${
                                    candidateImportFromFileUI &&
                                    values.courses.length === 1
                                      ? "500px"
                                      : ""
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
                      <div
                        className={"w-100 d-flex flex-row justify-content-end"}
                      >
                        <Button
                          type={"submit"}
                          color="primary"
                          className={"mr-3"}
                          disabled={candidateSelector.isLoading}
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
                          {candidateSelector.isLoading ? (
                            <Loader />
                          ) : (
                            <>{t("buttons.save")}</>
                          )}
                        </Button>
                      </div>
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
