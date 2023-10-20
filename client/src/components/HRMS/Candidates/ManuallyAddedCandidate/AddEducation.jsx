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
import { Field, Formik, Form, FieldArray, getIn, ErrorMessage } from "formik";
import { useDispatch, useSelector } from "react-redux";
import {
  addCandidateEducation,
  getCandidateEducationById,
} from "../../../../redux/actions/candidatesAction";
import "react-toastify/dist/ReactToastify.css";
import { useHistory } from "react-router-dom";
import { Loader } from "../../../constants/loaders";
import asterik from "../../../../assets/images/asterisk.png";
import Toast from "../../../constants/toast";
import { getEducationDegree } from "../../../../redux/actions/educationActions";
import * as moment from "moment/moment";
import "moment/locale/et";
import {
  FormValidationErrors,
  SubmitFormValidationErrors,
} from "../../../constants/errors";
import { DayPicker, YearPicker } from "react-dropdown-date";
import { baseURL } from "../../../Shared/baseURL";
import axios from "axios";
// import { defaultLayoutPlugin } from "@react-pdf-viewer/default-layout";
// import { Viewer, Worker } from "@react-pdf-viewer/core";
import { AiOutlineLeft, AiOutlineRight } from "react-icons/ai";
import { useTranslation } from "react-i18next";
import {
  highlightFieldColor,
  highlightClickedFieldColor,
} from "../../../Shared/Colors";
import PdfViewer from "./PdfViewer";
import { EducationValidationSchema } from "../validations/formValidation";
import { validateHtmlTags } from "../validations/validateHtmlTags";

// funcations
import handleCandidateEducationCvData from "./functions/candidateCvDataFunctions/educationCvData";
import { handleStartMonths, handleEndMonths } from "./functions/handleMonths";
import {
  handleClickedSearchKeywords,
  handleClickedBlurSearchKeywords,
} from "./functions/handleClickedSearchKeywords";

const Education = ({
  candidateCvID,
  cvData,
  viewPdfFile,
  setTabsId,
  cvFile,
}) => {
  const dispatch = useDispatch();
  const history = useHistory();

  const { t } = useTranslation();

  const initialValues = {
    education: [
      {
        candidatecv_id: candidateCvID,
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
      },
    ],
  };

  const [formValues, setFormValues] = useState(initialValues);
  const [newEducationData, setNewEducationData] = useState([]);
  const [educationDegrees, setEducationDegrees] = useState([]);

  // ERRORS STATE
  const [errors, setErrors] = useState({});
  const [formErrors, setFormErrors] = useState({});

  const [educationLevels, setEducationLevels] = useState([]);
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
  // const defaultLayoutPluginInstance = defaultLayoutPlugin();

  useEffect(() => {
    const fetchEducationDegrees = async () => {
      try {
        const response = await dispatch(getEducationDegree());
      } catch (error) {
        throw error;
      }
    };
    fetchEducationDegrees();
  }, []);

  useEffect(() => {
    const fetchCandidateEducationById = async () => {
      try {
        const response = await dispatch(
          getCandidateEducationById(candidateCvID)
        );

        handleCandidateEducationCvData(
          cvData,
          setFormValues,
          setSearchKeywords,
          educationDegrees,
          candidateCvID
        );
      } catch (error) {
        throw error;
      }
    };
    fetchCandidateEducationById();
  }, []);

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
          );
      } catch (error) {
        throw error;
      }
    };
    fetchEducationLevels();
  }, []);

  const educationSelector = useSelector((state) => state.candidates);
  const getEducationByIdSelector = useSelector(
    (state) => state.candidates.data.CandidateCVEducation
  );
  const getEducationDegreeSelector = useSelector(
    (state) => state.educationDegree.education.data
  );
  const settingsSelector = useSelector((state) => state.settings);

  useEffect(() => {
    if (getEducationByIdSelector) {
      setNewEducationData(Object.values(getEducationByIdSelector));
    }

    if (getEducationDegreeSelector) {
      setEducationDegrees(getEducationDegreeSelector.Education_Degrees);
    }
  }, [getEducationDegreeSelector, getEducationDegreeSelector]);

  useEffect(() => {
    if (formErrors?.length > 0) {
      document.getElementById("error-messages").scrollIntoView();
    }
  }, [formErrors]);

  const handleSubmit = (values) => {
    // validate
    let error = {};

    values.education.map((item) => {
      error = validateHtmlTags(item, setFormErrors);
    });

    // form submit if there is no error
    if (Object.keys(error).length === 0) {
      setFormErrors({});
      values.education.map((item, index) => {
        if (item.still_studying === true) {
          item.still_studying = 1;
          item.ending_year = "";
          item.ending_month = "";
          item.ending_day = "";
        } else {
          item.still_studying = 0;
        }

        if (item.speciality === undefined) {
          item.speciality = "";
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

        if (item.additonal_information === undefined) {
          item.additonal_information = "";
        }
      });

      const formData = new FormData();

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
      dispatch(addCandidateEducation(formData, setErrors, setTabsId));
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
            validationSchema={EducationValidationSchema}
            onSubmit={(values) => handleSubmit(values)}
            children={({ values, setFieldValue, errors, touched }) => (
              <Form className={"w-100"} id={"add-job-history-form"}>
                <fieldset disabled={educationSelector.isLoading}>
                  <FormGroup>
                    <FieldArray
                      name={"education"}
                      render={(arrayHelpers) => (
                        <>
                          <Row>
                            <Col md={candidateImportFromFileUI ? 6 : 12}>
                              {values.education.map((items, index) => (
                                <>
                                  <Row key={index}>
                                    <Field
                                      type={"hidden"}
                                      name={`education.${index}.candidatecv_id`}
                                      value={4}
                                    />
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
                                        id={`institute-name.${index}`}
                                        name={`education.${index}.institute`}
                                        className={"form-control"}
                                        placeholder={"Õppeasutus"}
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
                                            formValues.education &&
                                            formValues.education?.length > 0 &&
                                            formValues.education[index]
                                              ?.institute &&
                                            formValues.education[index]
                                              ?.institute[index]
                                              ? highlightFieldColor
                                              : ""
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
                                          "candidates.bookmarks.education.speciality"
                                        )}
                                      </Label>
                                      <Field
                                        type={"text"}
                                        id={`speciality.${index}`}
                                        name={`education.${index}.speciality`}
                                        className={"form-control"}
                                        placeholder={"Eriala"}
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
                                            formValues.education &&
                                            formValues.education?.length > 0 &&
                                            formValues.education[index]
                                              ?.speciality &&
                                            formValues.education[index]
                                              ?.speciality[index]
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
                                        ? "flex-column"
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
                                              handleStartMonths(
                                                month,
                                                setStartMonth
                                              );
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
                                              values.education[index]
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
                                            id={"year"}
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
                                              handleEndMonths(
                                                month,
                                                setEndMonth
                                              );
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
                                              values.education[index]
                                                .ending_month
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
                                          <input
                                            type="checkbox"
                                            name={`education.${index}.still_studying`}
                                            id={"still-studying"}
                                            onChange={(e) => {
                                              handleCheckbox(e, index, values);
                                              setFieldValue(
                                                `education.${index}.still_studying`,
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
                                            "candidates.bookmarks.education.stillStudying"
                                          )}
                                        </Label>
                                      </FormGroup>
                                    </Col>
                                  </Row>
                                  <Row className={`w-100`}>
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
                                        style={{
                                          background: `${
                                            formValues.education &&
                                            formValues.education?.length > 0 &&
                                            formValues.education[index]
                                              ?.additonal_information &&
                                            formValues.education[index]
                                              ?.additonal_information[index]
                                              ? highlightFieldColor
                                              : ""
                                          }`,
                                        }}
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
                                  </Row>
                                  {values.education.length > 1 && (
                                    <Button
                                      type="button"
                                      color={"danger"}
                                      className={"mb-2 mt-2 ml-1"}
                                      onClick={() => arrayHelpers.remove(index)}
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
                                </>
                              ))}
                            </Col>
                            <Col md={6}>
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
                            className={`mb-2 mt-2`}
                            onClick={() => {
                              arrayHelpers.push({
                                candidatecv_id: candidateCvID,
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
                  <div className={"w-100 d-flex flex-row justify-content-end"}>
                    <Button
                      type={"submit"}
                      color="primary"
                      className={"mr-3"}
                      disabled={educationSelector.isLoading}
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
                      {educationSelector.isLoading ? (
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

export default Education;
