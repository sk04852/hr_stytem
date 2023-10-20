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
import "../candidate.css";
import { useDispatch, useSelector } from "react-redux";
import {
  addCandidateJobHistory,
  getCandidateJobHistoryById,
} from "../../../../redux/actions/candidatesAction";
import { Field, Formik, Form, FieldArray, getIn } from "formik";
import { Loader } from "../../../constants/loaders";
import asterik from "../../../../assets/images/asterisk.png";
import Toast from "../../../constants/toast";
import * as moment from "moment/moment";
import "moment/locale/et";
import {
  FormValidationErrors,
  SubmitFormValidationErrors,
} from "../../../constants/errors";
import { DayPicker, YearPicker } from "react-dropdown-date";
// import { Viewer, Worker } from "@react-pdf-viewer/core";
// import { defaultLayoutPlugin } from "@react-pdf-viewer/default-layout";
import { AiOutlineLeft, AiOutlineRight } from "react-icons/ai";
import { useTranslation } from "react-i18next";
import PdfViewer from "./PdfViewer";
import {
  highlightFieldColor,
  highlightClickedFieldColor,
} from "../../../Shared/Colors";
import { JobHistorySchema } from "../validations/formValidation";
import { validateHtmlTags } from "../validations/validateHtmlTags";

// functions
import handleCandidateJobHistoryCvData from "./functions/candidateCvDataFunctions/jobHistoryCvData";
import { handleStartMonths, handleEndMonths } from "./functions/handleMonths";
import {
  handleClickedSearchKeywords,
  handleClickedBlurSearchKeywords,
} from "./functions/handleClickedSearchKeywords";

const JobHistory = ({
  candidateCvID,
  cvData,
  viewPdfFile,
  setTabsId,
  cvFile,
}) => {
  let initialValues = {
    jobs: [
      {
        candidatecv_id: candidateCvID,
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
      },
    ],
  };

  const dispatch = useDispatch();

  const { t } = useTranslation();

  // const defaultLayoutPluginInstance = defaultLayoutPlugin();

  const [formValues, setFormValues] = useState(initialValues);
  const [newJob, setNewJob] = useState([]);

  // ERRORS STATE
  const [errors, setErrors] = useState({});
  const [formErrors, setFormErrors] = useState({});

  const [candidateImportFromFileUI, setCandidateImportFromFileUI] =
    useState(false);

  // Starting and Ending Date States
  const [startYear, setStartYear] = useState(null);
  const [startMonth, setStartMonth] = useState(null);
  const [startDay, setStartDay] = useState(null);
  const [endYear, setEndYear] = useState(null);
  const [endMonth, setEndMonth] = useState(null);
  const [endDay, setEndDay] = useState(null);

  const [searchKeywords, setSearchKeywords] = useState([]);
  const [clickedSearchKeywords, setClickedSearchKeywords] = useState([]);

  let estMonths = moment.months();

  // const jobHistorySelector = useSelector((state) => state.candidates);
  const settingsSelector = useSelector((state) => state.settings);

  useEffect(() => {
    const fetCandidateJobHistoryById = async () => {
      try {
        const response = await dispatch(
          getCandidateJobHistoryById(candidateCvID)
        );

        // handle candidate job history cv data funcation
        handleCandidateJobHistoryCvData(
          cvData,
          setFormValues,
          setSearchKeywords,
          candidateCvID
        );
      } catch (error) {
        throw error;
      }
    };
    fetCandidateJobHistoryById();
  }, []);

  const jobHistoryByIdSelector = useSelector(
    (state) => state.candidates.data.CandidateCVJobHistories
  );
  const candidateSelector = useSelector((state) => state.candidates);

  useEffect(() => {
    if (jobHistoryByIdSelector) {
      setNewJob(Object.values(jobHistoryByIdSelector));
    }
  }, [jobHistoryByIdSelector]);

  useEffect(() => {
    if (formErrors?.length > 0) {
      document.getElementById("error-messages").scrollIntoView();
    }
  }, [formErrors]);

  const handleSubmit = (values) => {
    // validate
    let error = {};

    values.jobs.map((item) => {
      error = validateHtmlTags(item, setFormErrors);
    });

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

        if (item.starting_month === undefined) {
          item.starting_month = "";
        }

        if (item.starting_day === undefined) {
          item.starting_day = "";
        }

        if (item.ending_month === undefined) {
          item.ending_month = "";
        }

        if (item.ending_day === undefined) {
          item.ending_day = "";
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

      dispatch(addCandidateJobHistory(formData, setErrors, setTabsId));
    }
  };

  const handleStillWorkingCheckbox = (e, index, values) => {
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
        } else {
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
          setEndDay(null);
        }
      }
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
          <Formik
            initialValues={formValues}
            enableReinitialize={true}
            validationSchema={JobHistorySchema}
            onSubmit={(values) => handleSubmit(values)}
            children={({ values, setFieldValue, errors, touched }) => (
              <Form className={"w-100"} id={"add-job-history-form"}>
                <fieldset disabled={candidateSelector.isLoading}>
                  <FormGroup>
                    <FieldArray
                      name={"jobs"}
                      render={(arrayHelpers) => (
                        <>
                          <Row>
                            <Col md={candidateImportFromFileUI ? 6 : 12}>
                              {values.jobs.map((items, index) => (
                                <>
                                  <Row key={index}>
                                    <Field
                                      type={"hidden"}
                                      name={`jobs.${index}.candidatecv_id`}
                                      value={4}
                                    />
                                    <Col
                                      md={candidateImportFromFileUI ? 6 : 3}
                                      sm={3}
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
                                        id={`company-name.${index}`}
                                        name={`jobs.${index}.company_name`}
                                        className={`form-control`}
                                        placeholder={"Ettevõtte nimetus"}
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
                                            formValues.jobs &&
                                            formValues.jobs?.length > 0 &&
                                            formValues.jobs[index]
                                              ?.company_name &&
                                            formValues.jobs[index]
                                              ?.company_name[index]
                                              ? highlightFieldColor
                                              : ""
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
                                    <Col
                                      md={candidateImportFromFileUI ? 6 : 3}
                                      sm={3}
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
                                        id={`designation.${index}`}
                                        name={`jobs.${index}.designation`}
                                        className={`form-control`}
                                        placeholder={"Amet"}
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
                                            formValues.jobs &&
                                            formValues.jobs?.length > 0 &&
                                            formValues.jobs[index]
                                              ?.designation &&
                                            formValues.jobs[index]?.designation[
                                              index
                                            ]
                                              ? highlightFieldColor
                                              : ""
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
                                    <Col
                                      md={candidateImportFromFileUI ? 12 : 3}
                                      sm={3}
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
                                          "candidates.bookmarks.jobHistory.workPlace"
                                        )}
                                      </Label>
                                      <Field
                                        type={"text"}
                                        id={"work-place"}
                                        name={`jobs.${index}.work_place`}
                                        className={`form-control ${
                                          candidateImportFromFileUI
                                            ? "mb-3"
                                            : ""
                                        }`}
                                        placeholder={"Töökoha aadress"}
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
                                            formValues.jobs &&
                                            formValues.jobs?.length > 0 &&
                                            formValues.jobs[index]
                                              ?.work_place &&
                                            formValues.jobs[index]?.work_place[
                                              index
                                            ]
                                              ? highlightFieldColor
                                              : ""
                                          }`,
                                        }}
                                      />
                                    </Col>
                                    <Col
                                      md={candidateImportFromFileUI ? 12 : 3}
                                      sm={3}
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
                                          "candidates.bookmarks.jobHistory.description"
                                        )}
                                      </Label>
                                      <Field
                                        as={"textarea"}
                                        id={`description.${index}`}
                                        name={`jobs.${index}.description`}
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
                                        className={"form-control"}
                                        placeholder={"Tööülesannete kirjeldus"}
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
                                            formValues.jobs &&
                                            formValues.jobs?.length > 0 &&
                                            formValues.jobs[index]
                                              ?.description &&
                                            formValues.jobs[index]?.description[
                                              index
                                            ]
                                              ? highlightFieldColor
                                              : ""
                                          }`,
                                        }}
                                      />
                                    </Col>
                                  </Row>
                                  <Row
                                    className={`w-100 mt-2 ${
                                      candidateImportFromFileUI
                                        ? "flex-column p-2"
                                        : "flex-row"
                                    }`}
                                  >
                                    <Col
                                      md={candidateImportFromFileUI ? 12 : 4}
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
                                              handleStartMonths(
                                                month,
                                                setStartMonth
                                              );
                                              setFieldValue(
                                                `jobs.${index}.starting_month`,
                                                month.target.value
                                              );
                                            }}
                                            disabled={
                                              values.jobs[index]
                                                .starting_year === ""
                                            }
                                            defaultValue={
                                              values.jobs[index].starting_month
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
                                    <Col
                                      md={candidateImportFromFileUI ? 12 : 4}
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
                                            id={"year"}
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
                                            onChange={(month) => {
                                              handleEndMonths(
                                                month,
                                                setEndMonth
                                              );
                                              setFieldValue(
                                                `jobs.${index}.ending_month`,
                                                month.target.value
                                              );
                                            }}
                                            disabled={
                                              values.jobs[index].ending_year ===
                                              ""
                                            }
                                            defaultValue={
                                              values.jobs[index].ending_month
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
                                              values.jobs[index].ending_year ===
                                                "" ||
                                              values.jobs[index]
                                                .ending_month === ""
                                            }
                                            value={
                                              values.jobs[index].ending_year ===
                                                "" ||
                                              values.jobs[index]
                                                .ending_month === ""
                                                ? (values.jobs[
                                                    index
                                                  ].ending_day = "")
                                                : values.jobs[index].ending_day
                                            }
                                            onChange={(day) => {
                                              setFieldValue(
                                                `jobs.${index}.ending_day`,
                                                day
                                              );
                                              setEndDay(day);
                                            }}
                                            id={"day"}
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
                                          <input
                                            type="checkbox"
                                            name={`jobs.${index}.still_working`}
                                            id={"still-working"}
                                            onChange={(e) => {
                                              handleStillWorkingCheckbox(
                                                e,
                                                index,
                                                values
                                              );
                                              setFieldValue(
                                                `jobs.${index}.still_working`,
                                                e.target.checked
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
                                                  : "inherit"
                                              }`,
                                            }}
                                          />{" "}
                                          {t(
                                            "candidates.bookmarks.jobHistory.stillWorking"
                                          )}
                                        </Label>
                                      </FormGroup>
                                    </Col>
                                  </Row>
                                  <Row className={"w-100 flex-column"}>
                                    {values.jobs.length > 1 && (
                                      <div>
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
                                            "candidates.bookmarks.jobHistory.deleteJob"
                                          )}
                                        </Button>
                                      </div>
                                    )}
                                  </Row>
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
                              values.jobs.length === 1
                                ? "position-absolute"
                                : ""
                            }`}
                            onClick={() => {
                              arrayHelpers.push({
                                candidatecv_id: candidateCvID,
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
                              top: `${
                                candidateImportFromFileUI &&
                                values.jobs.length === 1
                                  ? "650px"
                                  : ""
                              }`,
                            }}
                          >
                            {t("candidates.bookmarks.jobHistory.addMoreJobs")}
                          </Button>
                        </>
                      )}
                    />
                  </FormGroup>
                  <div className={"w-100 d-flex flex-row justify-content-end"}>
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
        </CardBody>
      </Card>
      <Toast />
    </>
  );
};

export default JobHistory;
