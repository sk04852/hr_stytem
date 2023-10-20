import React, { useEffect, useRef, useState } from "react";
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
  Alert,
} from "reactstrap";
import "../candidate.css";
import { Field, Formik, Form, FieldArray } from "formik";
import { useDispatch, useSelector } from "react-redux";
import { addCandidates } from "../../../../redux/actions/candidatesAction";
import asterik from "../../../../assets/images/asterisk.png";
import { Loader } from "../../../constants/loaders";
import Toast from "../../../constants/toast";
import { getStatus } from "../../../../redux/actions/statusActions";
import { getNationalities } from "../../../../redux/actions/nationalitiesActions";
import * as moment from "moment";
import "moment/locale/et";
import { AiOutlineLeft, AiOutlineRight } from "react-icons/ai";
import { TagsInput } from "react-tag-input-component";

// Core viewer
// import { Viewer, Worker } from "@react-pdf-viewer/core";

// Plugins
// import { defaultLayoutPlugin } from "@react-pdf-viewer/default-layout";

// Import styles
// import "@react-pdf-viewer/core/lib/styles/index.css";
// import "@react-pdf-viewer/default-layout/lib/styles/index.css";
import {
  FormValidationErrors,
  SubmitFormValidationErrors,
} from "../../../constants/errors";
import { Persist } from "formik-persist";
import { useTranslation } from "react-i18next";
import estoniaCities from "../../../Shared/EstoniaCities.json";
import ReactAvatarEditorModal from "../Modals/ReactAvatarEditorModal";
import PdfViewer from "./PdfViewer";
import { highlightFieldColor, whiteColor } from "../../../Shared/Colors";
import {
  handleClickedSearchKeywords,
  handleClickedBlurSearchKeywords,
} from "./functions/handleClickedSearchKeywords";
import { PersonalDataSchema } from "../validations/formValidation";
import { validateCandidatePersonalDataHtmlTags } from "../validations/validateHtmlTags";
import handleCandidatePersonalInfoCvData from "./functions/candidateCvDataFunctions/personalInfoCvData";
import { SUPPORTED_CANDIDATE_PERSONAL_INFO_FORMATS } from "../../../Shared/SupportedFilesExtenstions";

const AddPersonalData = ({
  setCandidateID,
  cvData,
  viewPdfFile,
  setCandidateEmail,
  setTabsId,
  cvFile,
}) => {
  const dispatch = useDispatch();

  const { t } = useTranslation();

  // Create new plugin instance
  // const defaultLayoutPluginInstance = defaultLayoutPlugin();
  const inputRef = useRef(null);
  const editInputRef = useRef(null);

  let specialCharacters = /[&\/\\#,+()$~%.'":*?<>{}]/g;

  const initialValues = {
    first_name: "",
    last_name: "",
    gender: "",
    dob: "",
    personal_information: "",
    personal_code: "",
    phone: "",
    email: "",
    location: "",
    nationalities: [],
    marital_status: "",
    age: "",
    // tags: [],
    description: "",
    source: "",
    mother_language: "",
    job_type: [],
    desired_job: "",
    desired_salary: "",
    desired_job_time: "",
    desired_job_location: "",
    action_id: 1,
    status: 0,
    consent: "",
    newsletter: "",
    photo: "",
    driving_licenses: [],
    recommendations: [],
    keywords: [],
    children_qty: "",
    // children_names: "",
  };

  const [formValues, setFormValues] = useState(initialValues);
  const [getAllLanguages, setGetAllLanguages] = useState([]);
  const [file, setFile] = useState("");
  const [candidateStatus, setCandidateStatus] = useState([]);
  const [nationalities, setNationalities] = useState([]);
  const [candidateImportFromFileUI, setCandidateImportFromFileUI] =
    useState(false);

  // ERRORS STATE
  const [errors, setErrors] = useState({});
  const [formErrors, setFormErrors] = useState({});

  const [calculatedAge, setCalculatedAge] = useState("");

  // PERSONAL CODE CHECKS
  const [checkPersonalCode, setCheckPersonalCode] = useState(false);
  const [checkPersonalCodeDate, setCheckPersonalCodeDate] = useState(false);
  const [checkPersonalCodeGender, setCheckPersonalCodeGender] = useState(false);
  const [checkPersonalCodeDOB, setCheckPersonalCodeDOB] = useState(false);
  const [checkPersonalCodeLength, setCheckPersonalCodeLength] = useState(false);

  // TAG STATES
  const [tags, setTags] = useState([]);
  const [inputVisible, setInputVisible] = useState(false);
  const [inputValue, setInputValue] = useState("");

  const [searchKeywords, setSearchKeywords] = useState([]);
  const [clickedSearchKeywords, setClickedSearchKeywords] = useState([]);

  const [fileTypeError, setFileTypeError] = useState(false);

  // highlight fields state
  const [highlightDrivingLicenseField, sethighlightDrivingLicenseField] =
    useState([]);
  const [highlightRecommendationsField, sethighlightRecommendationsField] =
    useState([]);

  const fetchNationalities = async () => {
    try {
      await dispatch(getNationalities());
    } catch (error) {
      throw error;
    }
  };

  useEffect(() => {
    fetchNationalities();
  }, []);

  const nationalitiesSelector = useSelector((state) => state.nationalities);
  const settingsSelector = useSelector((state) => state.settings);

  useEffect(() => {
    if (nationalitiesSelector.data) {
      setNationalities(nationalitiesSelector.data);
    }
  }, [nationalitiesSelector.data]);

  const getLanguageSelector = useSelector(
    (state) => state.languages.language.data
  );

  useEffect(() => {
    if (getLanguageSelector) {
      setGetAllLanguages(getLanguageSelector.Languages.data);
    }
  }, [getLanguageSelector]);

  useEffect(() => {
    if (inputVisible) {
      inputRef.current?.focus();
    }
  }, [inputVisible]);

  useEffect(() => {
    editInputRef.current?.focus();
  }, [inputValue]);

  useEffect(() => {
    dispatch(getStatus());
  }, []);

  const candidateSelector = useSelector((state) => state.candidates);
  const getStatusSelector = useSelector((state) => state.status.status.data);

  useEffect(() => {
    if (getStatusSelector) {
      setCandidateStatus(getStatusSelector.Actions.data);
    }
  }, [getStatusSelector]);

  useEffect(() => {
    if (cvData) {
      handleCandidatePersonalInfoCvData(
        cvData,
        formValues,
        setFormValues,
        sethighlightDrivingLicenseField,
        sethighlightRecommendationsField,
        tags,
        setTags,
        setSearchKeywords
      );
    }
  }, [cvData]);

  useEffect(() => {
    let candidateItems = JSON.parse(
      localStorage.getItem("candidate-personal-data-form")
    );
    if (candidateItems) {
      let candidateValues = candidateItems.values;

      setFormValues({
        ...formValues,
        first_name: candidateValues.first_name,
        last_name: candidateValues.last_name,
        gender: candidateValues.gender,
        dob: candidateValues.dob,
        personal_information: candidateValues.personal_information,
        personal_code: candidateValues.personal_code,
        phone: candidateValues.phone,
        email: candidateValues.email,
        location: candidateValues.location,
        age: candidateValues.age,
        // mother_language: candidateValues.mother_language,
        marital_status: candidateValues.marital_status,
        desired_job: candidateValues.desired_job,
        desired_salary: candidateValues.desired_salary,
        desired_job_time: candidateValues.desired_job_time,
        desired_job_location: candidateValues.desired_job_location,
        source: candidateValues.source,
        children_qty: candidateValues.children_qty,
        consent: candidateValues.consent,
        description: candidateValues.description,
        driving_licenses: candidateValues.driving_licenses,
        job_type: candidateValues.job_type,
        keywords: candidateValues.keywords,
        nationalities: candidateValues.nationalities,
        newsletter: candidateValues.newsletter,
        photo: candidateValues.photo,
        recommendations: candidateValues.recommendations,
        status: candidateValues.status,
      });
    }
  }, []);

  useEffect(() => {
    if (errors?.length > 0) {
      document.getElementById("error-messages").scrollIntoView();
    }
  }, [errors]);

  useEffect(() => {
    if (formErrors?.length > 0) {
      document.getElementById("error-messages").scrollIntoView();
    }
  }, [formErrors]);

  // console.log(formValues);

  const handleSubmit = (values) => {
    // validate
    let error = {};
    error = validateCandidatePersonalDataHtmlTags(values, tags, setFormErrors);

    // form submit if there is no error
    if (Object.keys(error).length === 0 && fileTypeError === false) {
      setFormErrors({});
      values["photo"] = file;

      if (values.consent === true) {
        values["consent"] = 1;
      } else {
        values["consent"] = 0;
      }

      if (values.newsletter === true) {
        values["newsletter"] = 1;
      } else {
        values["newsletter"] = 0;
      }

      setCandidateEmail(values.email);

      const formData = new FormData();
      formData.append("first_name", values.first_name);
      formData.append("last_name", values.last_name);
      formData.append("gender", values.gender);

      if (values.dob !== "" && values.dob !== null) {
        let dob = moment(values.dob, "DD-MM-YYYY").format("YYYY-MM-DD");
        formData.append("dob", dob);
      }
      formData.append(
        "age",
        values.age !== null ? values.age || calculatedAge : ""
      );
      formData.append("personal_information", values.personal_information);
      formData.append("personal_code", values.personal_code);
      formData.append("phone", values.phone);
      formData.append("email", values.email);
      formData.append("location", values.location);
      for (let i = 0; i < values.nationalities.length; i++) {
        formData.append(`nationalities[${i}]`, values.nationalities);
      }
      formData.append("marital_status", values.marital_status);

      if (tags.length > 0) {
        for (let i = 0; i < tags.length; i++) {
          formData.append("tags[]", tags[i]);
        }
      }

      formData.append("description", values.description);
      formData.append("source", values.source);
      formData.append("mother_language", values.mother_language);
      for (let i = 0; i < values.job_type.length; i++) {
        formData.append(`job_type[${i}]`, values.job_type[i]);
      }
      formData.append("desired_job", values.desired_job);
      formData.append("desired_salary", values.desired_salary);
      formData.append("desired_job_time", values.desired_job_time);
      formData.append("desired_job_location", values.desired_job_location);
      formData.append("action_id", values.action_id);
      formData.append("status", values.status);
      formData.append("consent", values.consent);
      formData.append("newsletter", values.newsletter);
      formData.append("photo", values.photo);
      if (values.driving_licenses.length > 0) {
        for (let i = 0; i < values.driving_licenses.length; i++) {
          Object.keys(values.driving_licenses[i]).map((items) => {
            formData.append(
              `driving_licenses[${i}][${items}]`,
              values.driving_licenses[i][items] === null
                ? ""
                : values.driving_licenses[i][items]
            );
          });
        }
      }
      for (let i = 0; i < values.recommendations.length; i++) {
        formData.append(`recommendations[${i}]`, values.recommendations);
      }
      for (let i = 0; i < values.keywords.length; i++) {
        formData.append(`keywords`, values.keywords.join(":::"));
      }
      formData.append("children_qty", values.children_qty);
      // formData.append("children_names", values.children_names);

      dispatch(
        addCandidates(
          formData,
          setCandidateID,
          setErrors,
          setFormValues,
          initialValues,
          setTabsId
        )
      );
      setCalculatedAge("");
      formValues.age = "";
    }
  };

  let formValuesRef = useRef(null);

  const handleBlur = (e) => {
    // 49409162218 PERSONAL CODE -> valid
    // 59409162218 PERSONAL CODE
    // 59217032956 PERSONAL CODE
    let personalCode = e.target.value;
    let currentDate = new Date();
    if (personalCode !== "") {
      // calculate DOB
      let result = personalCode.substring(1, 7);
      let genderCode = personalCode.substring(0, 1);
      if (genderCode == 3 || genderCode == 4) {
        result = "19" + result;
      } else if (genderCode == 5 || genderCode == 6) {
        result = "20" + result;
      } else {
        setCheckPersonalCodeGender(true);
      }

      let calculateDOB = moment(result, "YYYYMMDD");

      // Check DOB, Personal Code Length and Current Date is Valid or Not
      if (
        (calculateDOB.isValid() &&
          calculateDOB <= currentDate &&
          personalCode.length === 11) ||
        personalCode === ""
      ) {
        setCheckPersonalCode(false);
        setCheckPersonalCodeDate(false);
        setCheckPersonalCodeLength(false);
        setCheckPersonalCodeDOB(false);
        setCheckPersonalCodeGender(false);
        calculateDOB = calculateDOB.format("YYYY-MM-DD");

        // get gander
        if (genderCode === "3" || genderCode === "5") {
          formValues.gender = "1";
        } else if (genderCode === "4" || genderCode === "6") {
          formValues.gender = "2";
        } else {
          formValues.gender = "none";
        }
      } else {
        setCheckPersonalCode(true);
        setCheckPersonalCodeDate(false);
        setCheckPersonalCodeLength(false);
        // setFormValues({ ...formValuesRef.current.values, dob: "" });
        document.getElementById("personal-code").scrollIntoView();

        if (calculateDOB > currentDate) {
          setCheckPersonalCodeDate(true);
        }

        if (personalCode.length > 0 && personalCode.length !== 11) {
          setCheckPersonalCodeLength(true);
        }

        if (!calculateDOB.isValid()) {
          setCheckPersonalCodeDOB(true);
        }
      }

      // calculate AGE
      if (calculateDOB !== null) {
        let today = new Date();
        let birthDate = new Date(calculateDOB);
        let age = today.getFullYear() - birthDate.getFullYear();
        let m = today.getMonth() - birthDate.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
          age--;
        }
        if (age > 0) {
          formValues.age = age;
        } else {
          formValues.age = "";
        }
      }

      let firstNameValue = document.getElementById("first_name").value;
      let lastNameValue = document.getElementById("last_name").value;
      // let genderValue = document.getElementById("gender").value;
      let personalInformationValue = document.getElementById(
        "personal-information"
      ).value;
      let phoneValue = document.getElementById("phone").value;
      let emailValue = document.getElementById("email").value;
      let locationValue = document.getElementById("location").value;
      let maritalStatusValue = document.getElementById("marital-status").value;
      let childrenQtyValue = document.getElementById("children-qty").value;
      // let childrenNamesValue = document.getElementById("children-names").value;
      let sourceValue = document.getElementById("source").value;
      // let motherLanguageValue = document.getElementById("mother-language").value;
      let desiredJobValue = document.getElementById("desired-job").value;

      let desiredJobTimeValue =
        document.getElementById("desired-job-time").value;
      let desiredJobLocationValue = document.getElementById(
        "desired-job-location"
      ).value;
      let desiredJobSalaryValue =
        document.getElementById("desired-job-salary").value;
      let actionIdValue = document.getElementById("action-id").value;
      let actionStatusValue = document.getElementById("action-status").value;
      let newsletterValue = document.getElementById("newsletter").value;
      let consentValue = document.getElementById("consent").value;

      setFormValues({
        ...formValues,
        first_name: firstNameValue,
        last_name: lastNameValue,
        // gender: genderValue,
        dob: calculateDOB,
        personal_information: personalInformationValue,
        personal_code: personalCode,
        phone: phoneValue,
        email: emailValue,
        location: locationValue,
        marital_status: maritalStatusValue,
        children_qty: childrenQtyValue,
        // children_names: childrenNamesValue,
        source: sourceValue,
        // mother_language: motherLanguageValue,
        desired_job: desiredJobValue,
        desired_job_time: desiredJobTimeValue,
        desired_job_location: desiredJobLocationValue,
        desired_salary: desiredJobSalaryValue,
        action_id: actionIdValue,
        status: actionStatusValue,
        newsletter: newsletterValue,
        consent: consentValue,
      });
    } else {
      setCheckPersonalCode(false);
      setCheckPersonalCodeDate(false);
      setCheckPersonalCodeLength(false);
      setCheckPersonalCodeDOB(false);
      setCheckPersonalCodeGender(false);
      // setFormValues({
      //   ...formValuesRef.current.values,
      //   personal_code: "",
      //   dob: "",
      // });

      // if (cvData === undefined) {
      //   setFormValues({
      //     ...formValuesRef.current.values,
      //     personal_code: "",
      //     gender: "",
      //     dob: "",
      //     age: "",
      //   });
      // }
    }
  };

  const handleDobChange = (e) => {
    let selectedDate = e.target.value;
    formValues.dob = selectedDate;
    let ageElement = document.getElementById("age");
    // let dobElement = document.getElementById("dob");

    if (selectedDate === 0) {
      ageElement.value = "";
    }

    // calculate AGE
    if (selectedDate !== null) {
      let today = new Date();
      let birthDate = new Date(selectedDate);
      let age = today.getFullYear() - birthDate.getFullYear();
      let m = today.getMonth() - birthDate.getMonth();
      if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
        age--;
      }
      if (age > 0) {
        setFormValues({ ...formValuesRef.current.values, age: age });
        // formValues.age = age;
        setCalculatedAge(age);
      } else {
        setFormValues({ ...formValuesRef.current.values, age: "" });
        // formValues.age = "";
        ageElement.value = "";
      }
    }
  };

  const preventValues = (e) => {
    if (
      e.code === "NumpadSubtract" ||
      e.code === "NumpadAdd" ||
      e.code === "NumpadDivide" ||
      e.code === "NumpadMultiply" ||
      (e.code >= "KeyA" && e.code <= "KeyZ")
    ) {
      e.preventDefault();
    }
  };

  const handleAddressBlur = (e) => {
    let addressValue = e.target.value;

    if (addressValue.length > 0) {
      let replacedCharacters = addressValue
        ?.replaceAll(specialCharacters, " ")
        .trim();

      let matchedCitiesArr = [...tags];
      replacedCharacters?.split(" ").map((addressItem) => {
        estoniaCities.map((city) => {
          if (addressItem === city.city) {
            if (tags.length > 0) {
              if (!matchedCitiesArr.includes(city.city)) {
                matchedCitiesArr.push(city.city);
              }
            } else {
              matchedCitiesArr.push(city.city);
            }
          }
        });
      });

      setTags(matchedCitiesArr);
    }
  };

  const handleGenderBlur = (e) => {
    let genderValue = e.target.value;
    let genderArr = [...tags];

    if (genderValue === "1") {
      genderValue = "Mees";
    } else if (genderValue === "2") {
      genderValue = "Naine";
    }

    if (tags.length > 0) {
      if (!genderArr.includes(genderValue)) {
        let findMaleIndex = genderArr.indexOf("Mees" || "mees");
        let findFemaleIndex = genderArr.indexOf("Naine" || "naine");
        if (~findMaleIndex) {
          genderArr.splice(findMaleIndex, 1);
          genderArr.push(genderValue);
        } else if (~findFemaleIndex) {
          genderArr.splice(findFemaleIndex, 1);
          genderArr.push(genderValue);
        } else {
          genderArr.push(genderValue);
        }
      }
    } else {
      genderArr.push(genderValue);
    }

    setTags(genderArr);
  };

  const handleDesiredJobBlur = (e) => {
    let jobValue = e.target.value;
    let jobArr = [...tags];

    if (jobValue.length > 0) {
      if (!tags.includes(jobValue)) {
        jobArr.push(jobValue);
      }
      setTags(jobArr);
    }
  };

  const handleJobTypeClick = (e) => {
    let jobTypeValue = e.target.value;
    let checked = e.target.checked;

    let jobTypeArr = [...tags];

    if (checked) {
      if (!jobTypeArr.includes(jobTypeValue)) {
        if (jobTypeValue === "1") {
          jobTypeValue = "Täistööaeg";
        } else if (jobTypeValue === "2") {
          jobTypeValue = "Osaline tööaeg";
        }
        jobTypeArr.push(jobTypeValue);
      }
    } else {
      jobTypeArr.splice(tags.indexOf(jobTypeValue), 1);
    }
    setTags(jobTypeArr);
  };

  const handleDesiredJobTimeBlur = (e) => {
    let jobTimeValue = e.target.value;
    let jobTimeArr = [...tags];

    if (jobTimeValue.length > 0) {
      if (!tags.includes(jobTimeValue)) {
        jobTimeArr.push(jobTimeValue);
      }
      setTags(jobTimeArr);
    }
  };

  const handleDesiredJobLocationBlur = (e) => {
    let jobLocationValue = e.target.value;
    let jobLocationArr = [...tags];

    if (jobLocationValue.length > 0) {
      if (!tags.includes(jobLocationValue)) {
        jobLocationArr.push(jobLocationValue);
      }
      setTags(jobLocationArr);
    }
  };

  const handleDesiredJobSalaryBlur = (e) => {
    let jobSalaryValue = e.target.value;
    let jobSalaryArr = [...tags];

    if (jobSalaryValue.length > 0) {
      if (!tags.includes(jobSalaryValue)) {
        jobSalaryArr.push(jobSalaryValue);
      }
      setTags(jobSalaryArr);
    }
  };

  const handleDrivingLicenseBlur = (e) => {
    let drivingLicenseValue = e.target.value;
    let drivingLicenseArr = [...tags];

    if (drivingLicenseValue.length > 0) {
      if (!tags.includes(drivingLicenseValue)) {
        drivingLicenseArr.push(drivingLicenseValue);
      }
    }
    setTags(drivingLicenseArr);
  };

  const handleRemoveLicense = (index) => {
    let elem = document.getElementById(`driving-licenses.${index}.level`).value;

    // for remove tags if remove driving license
    let licenseArr = [...tags];
    licenseArr.splice(tags.indexOf(elem), 1);
    setTags(licenseArr);

    // for remove highlight if remove driving license
    let licenseFormValuesArr = [...highlightDrivingLicenseField];
    licenseFormValuesArr.splice(highlightDrivingLicenseField.indexOf(elem), 1);
    sethighlightDrivingLicenseField(licenseFormValuesArr);
  };

  const editorRef = useRef();
  const [image, setImage] = useState(null);
  const [scale, setScale] = useState(1);
  const [isCropAvatarModalOpen, setIsCropAvatarModalOpen] = useState(false);
  const handleImageChange = (event) => {
    let files = event.target.files[0];
    if (SUPPORTED_CANDIDATE_PERSONAL_INFO_FORMATS.includes(files.type)) {
      setFileTypeError(false);
      setImage(URL.createObjectURL(files));
      setIsCropAvatarModalOpen(true);
    } else {
      setFileTypeError(true);
    }
  };

  return (
    <>
      <ReactAvatarEditorModal
        image={image}
        editorRef={editorRef}
        isCropAvatarModalOpen={isCropAvatarModalOpen}
        setIsCropAvatarModalOpen={setIsCropAvatarModalOpen}
        scale={scale}
        setFile={setFile}
      />
      <Card>
        <CardHeader className="float-right">
          <h1>{t("candidates.bookmarks.personalInformationBookmark")}</h1>
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
                    {" "}
                    <AiOutlineRight /> {t("candidates.bookmarks.hideCV")}{" "}
                  </>
                ) : (
                  <>
                    {" "}
                    <AiOutlineLeft /> {t("candidates.bookmarks.showCV")}{" "}
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
            innerRef={formValuesRef}
            validationSchema={PersonalDataSchema}
            onSubmit={(values) => handleSubmit(values)}
            children={({ values, errors, touched, setFieldValue }) => (
              <Form id={"candidate-personal-data-form"}>
                <fieldset disabled={candidateSelector.isLoading}>
                  <Row>
                    <Col sm={6}>
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
                          {t(
                            "candidates.bookmarks.personalInformation.firstName"
                          )}{" "}
                          <img
                            src={asterik}
                            height={10}
                            width={10}
                            className="mt-n2"
                          />
                        </Label>
                        <Field
                          type="text"
                          name="first_name"
                          id="first_name"
                          placeholder="Eesnimi"
                          className={"form-control"}
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
                          // required
                          style={{
                            fontSize: `${
                              settingsSelector.FontSize === "Large"
                                ? "large"
                                : settingsSelector.FontSize === "Extra Large"
                                ? "x-large"
                                : "14px"
                            }`,
                            // background: fieldBgColor,
                          }}
                        />
                        {errors.first_name && touched.first_name ? (
                          <div style={{ color: "red" }}>
                            {errors.first_name}
                          </div>
                        ) : null}
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
                          {t(
                            "candidates.bookmarks.personalInformation.lastName"
                          )}{" "}
                          <img
                            src={asterik}
                            height={10}
                            width={10}
                            className="mt-n2"
                          />
                        </Label>
                        <Field
                          type="text"
                          name="last_name"
                          id="last_name"
                          placeholder="Perekonnanimi"
                          className={"form-control"}
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
                              settingsSelector.FontSize === "Large"
                                ? "large"
                                : settingsSelector.FontSize === "Extra Large"
                                ? "x-large"
                                : "14px"
                            }`,
                            // background: fieldBgColor,
                          }}
                        />
                        {errors.last_name && touched.last_name ? (
                          <div style={{ color: "red" }}>{errors.last_name}</div>
                        ) : null}
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
                          {t("candidates.candidateGender")}{" "}
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
                          name={"gender"}
                          id={"gender"}
                          onBlur={(e) => {
                            handleGenderBlur(e);
                            handleClickedBlurSearchKeywords(
                              e,
                              cvData,
                              setClickedSearchKeywords
                            );
                          }}
                          onClick={(e) =>
                            handleClickedSearchKeywords(
                              e,
                              cvData,
                              setClickedSearchKeywords
                            )
                          }
                          required
                          style={{
                            fontSize: `${
                              settingsSelector.FontSize === "Large"
                                ? "large"
                                : settingsSelector.FontSize === "Extra Large"
                                ? "x-large"
                                : "14px"
                            }`,
                            // background: fieldBgColor,
                          }}
                        >
                          <option value={""}>Ei ole valitud</option>
                          <option value={"1"}>Mees</option>
                          <option value={"2"}>Naine</option>
                        </Field>
                        {errors.gender && touched.gender ? (
                          <div style={{ color: "red" }}>{errors.gender}</div>
                        ) : null}
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
                          {t("candidates.bookmarks.personalInformation.dob")}
                        </Label>
                        <Field
                          type="date"
                          name="dob"
                          id="dob"
                          max={"3000-01-01"}
                          className={"form-control"}
                          placeholder="Sünniaeg"
                          onFocus={(e) => handleDobChange(e)}
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
                              settingsSelector.FontSize === "Large"
                                ? "large"
                                : settingsSelector.FontSize === "Extra Large"
                                ? "x-large"
                                : "14px"
                            }`,
                            // background: fieldBgColor,
                          }}
                        />
                        {errors.dob && touched.dob ? (
                          <div style={{ color: "red" }}>{errors.dob}</div>
                        ) : null}
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
                          {t(
                            "candidates.bookmarks.personalInformation.personalInformation"
                          )}
                        </Label>
                        <Field
                          as={"textarea"}
                          name="personal_information"
                          id="personal-information"
                          className={"form-control"}
                          placeholder="Palun kirjuta siia"
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
                              settingsSelector.FontSize === "Large"
                                ? "large"
                                : settingsSelector.FontSize === "Extra Large"
                                ? "x-large"
                                : "14px"
                            }`,
                            // background: fieldBgColor,
                          }}
                        />
                        {errors.personal_information &&
                        touched.personal_information ? (
                          <div style={{ color: "red" }}>
                            {errors.personal_information}
                          </div>
                        ) : null}
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
                          {t(
                            "candidates.bookmarks.personalInformation.personalCode"
                          )}
                        </Label>
                        <Field
                          type="text"
                          name="personal_code"
                          id="personal-code"
                          className={"form-control"}
                          placeholder="XXXXXXXXXXX"
                          onBlur={(e) => handleBlur(e)}
                          required={checkPersonalCode ? true : false}
                          maxLength={"11"}
                          style={{
                            fontSize: `${
                              settingsSelector.FontSize === "Large"
                                ? "large"
                                : settingsSelector.FontSize === "Extra Large"
                                ? "x-large"
                                : "14px"
                            }`,
                            // background: fieldBgColor,
                          }}
                        />
                        {errors.personal_code && touched.personal_code ? (
                          <div style={{ color: "red" }}>
                            {errors.personal_code}
                          </div>
                        ) : null}
                        {checkPersonalCode === true && (
                          <span style={{ color: "red" }}>
                            {t(
                              "formValidationMessages.personalCode.incorrectCode"
                            )}
                            {checkPersonalCodeDate === true && (
                              <>
                                <br />
                                {t(
                                  "formValidationMessages.personalCode.greaterYear"
                                )}
                              </>
                            )}
                            {checkPersonalCodeLength === true && (
                              <>
                                <br />
                                {t("formValidationMessages.personalCode.limit")}
                              </>
                            )}
                            {checkPersonalCodeGender === true && (
                              <>
                                <br />
                                {t(
                                  "formValidationMessages.personalCode.notMatch"
                                )}
                              </>
                            )}
                            {checkPersonalCodeDOB === true && (
                              <>
                                <br />
                                {t(
                                  "formValidationMessages.personalCode.notMatchWithDOB"
                                )}
                              </>
                            )}
                          </span>
                        )}
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
                          {t("candidates.candidatePhone")}
                        </Label>
                        <Field
                          type="text"
                          name="phone"
                          id="phone"
                          className={"form-control"}
                          placeholder="+3725XXXXXXX"
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
                              settingsSelector.FontSize === "Large"
                                ? "large"
                                : settingsSelector.FontSize === "Extra Large"
                                ? "x-large"
                                : "14px"
                            }`,
                            // background: fieldBgColor,
                          }}
                        />
                        {errors.phone && touched.phone ? (
                          <div style={{ color: "red" }}>{errors.phone}</div>
                        ) : null}
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
                          {t("candidates.bookmarks.personalInformation.email")}{" "}
                          <img
                            src={asterik}
                            height={10}
                            width={10}
                            className="mt-n2"
                          />
                        </Label>
                        <Field
                          type="email"
                          name="email"
                          id="email"
                          className={"form-control"}
                          placeholder="email@email.com"
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
                              settingsSelector.FontSize === "Large"
                                ? "large"
                                : settingsSelector.FontSize === "Extra Large"
                                ? "x-large"
                                : "14px"
                            }`,
                            // background: fieldBgColor,
                          }}
                        />
                        {errors.email && touched.email ? (
                          <div style={{ color: "red" }}>{errors.email}</div>
                        ) : null}
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
                          {t(
                            "candidates.bookmarks.personalInformation.address"
                          )}
                        </Label>
                        <Field
                          type="text"
                          name="location"
                          id="location"
                          className={"form-control"}
                          placeholder="Tänav, maja, korter, linn, riik"
                          onClick={(e) =>
                            handleClickedSearchKeywords(
                              e,
                              cvData,
                              setClickedSearchKeywords
                            )
                          }
                          onBlur={(e) => {
                            handleAddressBlur(e);
                            handleClickedBlurSearchKeywords(
                              e,
                              cvData,
                              setClickedSearchKeywords
                            );
                          }}
                          style={{
                            fontSize: `${
                              settingsSelector.FontSize === "Large"
                                ? "large"
                                : settingsSelector.FontSize === "Extra Large"
                                ? "x-large"
                                : "14px"
                            }`,
                            // background: fieldBgColor,
                          }}
                        />
                        {errors.location && touched.location ? (
                          <div style={{ color: "red" }}>{errors.location}</div>
                        ) : null}
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
                          {t(
                            "candidates.bookmarks.personalInformation.maritalStatus"
                          )}
                        </Label>
                        <Field
                          type={"text"}
                          name="marital_status"
                          id="marital-status"
                          className={"form-control"}
                          placeholder="Perekonnaseis"
                          style={{
                            fontSize: `${
                              settingsSelector.FontSize === "Large"
                                ? "large"
                                : settingsSelector.FontSize === "Extra Large"
                                ? "x-large"
                                : "14px"
                            }`,
                            // background: fieldBgColor,
                          }}
                        />
                        {errors.marital_status && touched.marital_status ? (
                          <div style={{ color: "red" }}>
                            {errors.marital_status}
                          </div>
                        ) : null}
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
                          {t(
                            "candidates.bookmarks.personalInformation.childrenQuantity"
                          )}
                        </Label>
                        <Field
                          type="text"
                          name="children_qty"
                          id="children-qty"
                          className={"form-control"}
                          placeholder="0"
                          min={0}
                          maxLength={2}
                          onKeyPress={preventValues}
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
                        {errors.children_qty && touched.children_qty ? (
                          <div style={{ color: "red" }}>
                            {t("formValidationMessages.childrenQuantity")}
                          </div>
                        ) : null}
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
                          {t("candidates.bookmarks.personalInformation.age")}
                        </Label>
                        <Field
                          type="text"
                          name="age"
                          id="age"
                          className={"form-control"}
                          placeholder="Vanus"
                          min={0}
                          maxLength={3}
                          onKeyPress={preventValues}
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
                        {errors.age && touched.age ? (
                          <div style={{ color: "red" }}>
                            {t("formValidationMessages.age")}
                          </div>
                        ) : null}
                      </FormGroup>
                    </Col>
                    {candidateImportFromFileUI ? (
                      <>
                        {viewPdfFile ? (
                          <PdfViewer
                            cvFile={cvFile}
                            cvData={cvData}
                            searchKeywords={searchKeywords}
                            clickedSearchKeywords={clickedSearchKeywords}
                          />
                        ) : (
                          <Col sm={4}>
                            <FormGroup>
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
                                  "candidates.bookmarks.personalInformation.source"
                                )}
                              </Label>
                              <Field
                                type="text"
                                name="source"
                                id="source"
                                className={"form-control"}
                                placeholder="cvkeskus.ee, facebook, google..."
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
                              {errors.source && touched.source ? (
                                <div style={{ color: "red" }}>
                                  {errors.source}
                                </div>
                              ) : null}
                            </FormGroup>
                            <FormGroup>
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
                                  "candidates.bookmarks.personalInformation.desiredJob"
                                )}
                              </Label>
                              <Field
                                type={"text"}
                                className={"form-control"}
                                name={"desired_job"}
                                id={"desired-job"}
                                onBlur={(e) => {
                                  handleDesiredJobBlur(e);
                                  handleClickedBlurSearchKeywords(
                                    e,
                                    cvData,
                                    setClickedSearchKeywords
                                  );
                                }}
                                onClick={(e) =>
                                  handleClickedSearchKeywords(
                                    e,
                                    cvData,
                                    setClickedSearchKeywords
                                  )
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
                                  // background: fieldBgColor,
                                }}
                              />
                              {errors.desired_job && touched.desired_job ? (
                                <div style={{ color: "red" }}>
                                  {errors.desired_job}
                                </div>
                              ) : null}
                            </FormGroup>
                            <FormGroup>
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
                                  "candidates.bookmarks.personalInformation.desiredJobType"
                                )}
                              </Label>
                              <label
                                className={"mr-3"}
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
                                <Field
                                  type="checkbox"
                                  name="job_type"
                                  id="job-type-full-time"
                                  value="1"
                                  onClick={(e) => handleJobTypeClick(e)}
                                />{" "}
                                {t(
                                  "candidates.bookmarks.personalInformation.fullTime"
                                )}
                              </label>
                              <label
                                className={"mr-3"}
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
                                <Field
                                  type="checkbox"
                                  name="job_type"
                                  id="job-type-part-time"
                                  value="2"
                                  onClick={(e) => handleJobTypeClick(e)}
                                />{" "}
                                {t(
                                  "candidates.bookmarks.personalInformation.partTime"
                                )}
                              </label>
                              {/* <label>
                                <Field
                                  type="checkbox"
                                  name="job_type"
                                  value="null"
                                />{" "}
                                Ei ole valitud
                              </label> */}
                              {errors.job_type && touched.job_type ? (
                                <div style={{ color: "red" }}>
                                  {errors.job_type}
                                </div>
                              ) : null}
                            </FormGroup>
                            <FormGroup>
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
                                  "candidates.bookmarks.personalInformation.desiredJobTime"
                                )}
                              </Label>
                              <Field
                                type={"text"}
                                className={"form-control"}
                                name={"desired_job_time"}
                                id={"desired-job-time"}
                                onBlur={(e) => {
                                  handleDesiredJobTimeBlur(e);
                                  handleClickedBlurSearchKeywords(
                                    e,
                                    cvData,
                                    setClickedSearchKeywords
                                  );
                                }}
                                onClick={(e) =>
                                  handleClickedSearchKeywords(
                                    e,
                                    cvData,
                                    setClickedSearchKeywords
                                  )
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
                                  // background: fieldBgColor,
                                }}
                              />
                              {errors.desired_job_time &&
                              touched.desired_job_time ? (
                                <div style={{ color: "red" }}>
                                  {errors.desired_job_time}
                                </div>
                              ) : null}
                            </FormGroup>
                            <FormGroup>
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
                                  "candidates.bookmarks.personalInformation.desiredJobLocation"
                                )}
                              </Label>
                              <Field
                                type={"text"}
                                className={"form-control"}
                                name={"desired_job_location"}
                                id={"desired-job-location"}
                                onBlur={(e) => {
                                  handleDesiredJobLocationBlur(e);
                                  handleClickedBlurSearchKeywords(
                                    e,
                                    cvData,
                                    setClickedSearchKeywords
                                  );
                                }}
                                onClick={(e) =>
                                  handleClickedSearchKeywords(
                                    e,
                                    cvData,
                                    setClickedSearchKeywords
                                  )
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
                                  // background: fieldBgColor,
                                }}
                              />
                              {errors.desired_job_location &&
                              touched.desired_job_location ? (
                                <div style={{ color: "red" }}>
                                  {errors.desired_job_location}
                                </div>
                              ) : null}
                            </FormGroup>
                            <FormGroup>
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
                                  "candidates.bookmarks.personalInformation.desiredJobSalary"
                                )}
                              </Label>
                              <Field
                                type={"number"}
                                className={"form-control"}
                                name={"desired_salary"}
                                id={"desired-job-salary"}
                                onBlur={(e) => {
                                  handleDesiredJobSalaryBlur(e);
                                  handleClickedBlurSearchKeywords(
                                    e,
                                    cvData,
                                    setClickedSearchKeywords
                                  );
                                }}
                                onClick={(e) =>
                                  handleClickedSearchKeywords(
                                    e,
                                    cvData,
                                    setClickedSearchKeywords
                                  )
                                }
                                min={0}
                                style={{
                                  fontSize: `${
                                    settingsSelector.FontSize === "Large"
                                      ? "large"
                                      : settingsSelector.FontSize ===
                                        "Extra Large"
                                      ? "x-large"
                                      : "14px"
                                  }`,
                                  // background: fieldBgColor,
                                }}
                              />
                              {errors.desired_salary &&
                              touched.desired_salary ? (
                                <div style={{ color: "red" }}>
                                  {errors.desired_salary}
                                </div>
                              ) : null}
                            </FormGroup>
                            <FormGroup>
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
                                {t("candidates.candidatePhase")}
                              </Label>
                              <Field
                                as={"select"}
                                className="custom-select"
                                name={"action_id"}
                                id={"action-id"}
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
                                <option value={""}>Ei ole valitud</option>
                                {candidateStatus.map((items, index) => (
                                  <option value={items.id} key={index}>
                                    {items.name}
                                  </option>
                                ))}
                              </Field>
                              {errors.action_id && touched.action_id ? (
                                <div style={{ color: "red" }}>
                                  {errors.action_id}
                                </div>
                              ) : null}
                            </FormGroup>
                            <FormGroup>
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
                                  "candidates.bookmarks.personalInformation.status"
                                )}
                              </Label>
                              <Field
                                as={"select"}
                                className="custom-select"
                                name={"status"}
                                id={"action-status"}
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
                                <option value={"1"}>
                                  {t(
                                    "candidates.bookmarks.personalInformation.statusActive"
                                  )}
                                </option>
                                <option value={"0"}>
                                  {t(
                                    "candidates.bookmarks.personalInformation.statusInActive"
                                  )}
                                </option>
                              </Field>
                              {errors.status && touched.status ? (
                                <div style={{ color: "red" }}>
                                  {errors.status}
                                </div>
                              ) : null}
                            </FormGroup>
                            <FormGroup>
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
                                  "candidates.bookmarks.personalInformation.uploadPicture"
                                )}
                              </Label>
                              <Input
                                type="file"
                                id="file"
                                name="file"
                                // accept="image/png, image/jpg"
                                className={"form-control"}
                                onChange={(e) => handleImageChange(e)}
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
                              {fileTypeError && (
                                <span style={{ color: "red" }}>
                                  {t("errors.invalidFileType")}
                                </span>
                              )}
                              <Alert color={"info"} className={"mt-2"}>
                                <strong>{t("alerts.allowedFormats")}:</strong>{" "}
                                .jpg, .jpeg, .png, .bmp, .gif, .svg, or .webp
                              </Alert>
                            </FormGroup>

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
                                <Field
                                  type="checkbox"
                                  name={"newsletter"}
                                  id={"newsletter"}
                                />{" "}
                                {t(
                                  "candidates.bookmarks.personalInformation.newsLetter"
                                )}
                              </Label>
                              {errors.newsletter && touched.newsletter ? (
                                <div style={{ color: "red" }}>
                                  {errors.newsletter}
                                </div>
                              ) : null}
                            </FormGroup>
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
                                <Field
                                  type="checkbox"
                                  name={"consent"}
                                  id={"consent"}
                                />{" "}
                                {t(
                                  "candidates.bookmarks.personalInformation.consent"
                                )}
                              </Label>
                              {errors.consent && touched.consent ? (
                                <div style={{ color: "red" }}>
                                  {errors.consent}
                                </div>
                              ) : null}
                            </FormGroup>
                          </Col>
                        )}
                      </>
                    ) : (
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
                            {t(
                              "candidates.bookmarks.personalInformation.source"
                            )}
                          </Label>
                          <Field
                            type="text"
                            name="source"
                            id="source"
                            className={"form-control"}
                            placeholder="cvkeskus.ee, facebook, google..."
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
                          {errors.source && touched.source ? (
                            <div style={{ color: "red" }}>{errors.source}</div>
                          ) : null}
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
                            {t(
                              "candidates.bookmarks.personalInformation.desiredJob"
                            )}
                          </Label>
                          <Field
                            type={"text"}
                            className={"form-control"}
                            name={"desired_job"}
                            id={"desired-job"}
                            onBlur={(e) => {
                              handleDesiredJobBlur(e);
                              handleClickedBlurSearchKeywords(
                                e,
                                cvData,
                                setClickedSearchKeywords
                              );
                            }}
                            onClick={(e) =>
                              handleClickedSearchKeywords(
                                e,
                                cvData,
                                setClickedSearchKeywords
                              )
                            }
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                              // background: fieldBgColor,
                            }}
                          />
                          {errors.desired_job && touched.desired_job ? (
                            <div style={{ color: "red" }}>
                              {errors.desired_job}
                            </div>
                          ) : null}
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
                            {t(
                              "candidates.bookmarks.personalInformation.desiredJobType"
                            )}
                          </Label>
                          <label className={"mr-3"}>
                            <Field
                              type="checkbox"
                              name="job_type"
                              id="job-type-full-time"
                              value="1"
                              onClick={(e) => handleJobTypeClick(e)}
                            />{" "}
                            {t(
                              "candidates.bookmarks.personalInformation.fullTime"
                            )}
                          </label>
                          <label className={"mr-3"}>
                            <Field
                              type="checkbox"
                              name="job_type"
                              id="job-type-part-time"
                              value="2"
                              onClick={(e) => handleJobTypeClick(e)}
                            />{" "}
                            {t(
                              "candidates.bookmarks.personalInformation.partTime"
                            )}
                          </label>
                          {errors.job_type && touched.job_type ? (
                            <div style={{ color: "red" }}>
                              {errors.job_type}
                            </div>
                          ) : null}
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
                            {t(
                              "candidates.bookmarks.personalInformation.desiredJobTime"
                            )}
                          </Label>
                          <Field
                            type={"text"}
                            className={"form-control"}
                            name={"desired_job_time"}
                            id={"desired-job-time"}
                            onBlur={(e) => {
                              handleDesiredJobTimeBlur(e);
                              handleClickedBlurSearchKeywords(
                                e,
                                cvData,
                                setClickedSearchKeywords
                              );
                            }}
                            onClick={(e) =>
                              handleClickedSearchKeywords(
                                e,
                                cvData,
                                setClickedSearchKeywords
                              )
                            }
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                              // background: fieldBgColor,
                            }}
                          />
                          {errors.desired_job_time &&
                          touched.desired_job_time ? (
                            <div style={{ color: "red" }}>
                              {errors.desired_job_time}
                            </div>
                          ) : null}
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
                            {t(
                              "candidates.bookmarks.personalInformation.desiredJobLocation"
                            )}
                          </Label>
                          <Field
                            type={"text"}
                            className={"form-control"}
                            name={"desired_job_location"}
                            id={"desired-job-location"}
                            onBlur={(e) => {
                              handleDesiredJobLocationBlur(e);
                              handleClickedBlurSearchKeywords(
                                e,
                                cvData,
                                setClickedSearchKeywords
                              );
                            }}
                            onClick={(e) =>
                              handleClickedSearchKeywords(
                                e,
                                cvData,
                                setClickedSearchKeywords
                              )
                            }
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                              // background: fieldBgColor,
                            }}
                          />
                          {errors.desired_job_location &&
                          touched.desired_job_location ? (
                            <div style={{ color: "red" }}>
                              {errors.desired_job_location}
                            </div>
                          ) : null}
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
                            {t(
                              "candidates.bookmarks.personalInformation.desiredJobSalary"
                            )}
                          </Label>
                          <Field
                            type={"number"}
                            className={"form-control"}
                            name={"desired_salary"}
                            id={"desired-job-salary"}
                            onBlur={(e) => {
                              handleDesiredJobSalaryBlur(e);
                              handleClickedBlurSearchKeywords(
                                e,
                                cvData,
                                setClickedSearchKeywords
                              );
                            }}
                            onClick={(e) =>
                              handleClickedSearchKeywords(
                                e,
                                cvData,
                                setClickedSearchKeywords
                              )
                            }
                            min={0}
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                              // background: fieldBgColor,
                            }}
                          />
                          {errors.desired_salary && touched.desired_salary ? (
                            <div style={{ color: "red" }}>
                              {errors.desired_salary}
                            </div>
                          ) : null}
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
                            {t("candidates.candidatePhase")}
                          </Label>
                          <Field
                            as={"select"}
                            className="custom-select"
                            name={"action_id"}
                            id={"action-id"}
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
                            <option value={""}>Ei ole valitud</option>
                            {candidateStatus.map((items, index) => (
                              <option value={items.id} key={index}>
                                {items.name}
                              </option>
                            ))}
                          </Field>
                          {errors.action_id && touched.action_id ? (
                            <div style={{ color: "red" }}>
                              {errors.action_id}
                            </div>
                          ) : null}
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
                            {t(
                              "candidates.bookmarks.personalInformation.status"
                            )}
                          </Label>
                          <Field
                            as={"select"}
                            className="custom-select"
                            name={"status"}
                            id={"action-status"}
                          >
                            <option value={"1"}>
                              {t(
                                "candidates.bookmarks.personalInformation.statusActive"
                              )}
                            </option>
                            <option value={"0"}>
                              {t(
                                "candidates.bookmarks.personalInformation.statusInActive"
                              )}
                            </option>
                          </Field>
                          {errors.status && touched.status ? (
                            <div style={{ color: "red" }}>{errors.status}</div>
                          ) : null}
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
                            {t(
                              "candidates.bookmarks.personalInformation.uploadPicture"
                            )}
                          </Label>
                          <Input
                            type="file"
                            id="file"
                            name="file"
                            // accept="image/png, image/jpg"
                            className={"form-control"}
                            onChange={(e) => handleImageChange(e)}
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
                            <span style={{ color: "red" }}>
                              {t("errors.invalidFileType")}
                            </span>
                          )}
                          <Alert color={"info"} className={"mt-2"}>
                            <strong>{t("alerts.allowedFormats")}:</strong> .jpg,
                            .jpeg, .png, .bmp, .gif, .svg, or .webp
                          </Alert>
                        </FormGroup>

                        <FormGroup>
                          <Label
                            className="ml-3 mt-2"
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
                            <Field
                              type="checkbox"
                              name={"newsletter"}
                              id={"newsletter"}
                            />{" "}
                            {t(
                              "candidates.bookmarks.personalInformation.newsLetter"
                            )}
                            {errors.newsletter && touched.newsletter ? (
                              <div style={{ color: "red" }}>
                                {errors.newsletter}
                              </div>
                            ) : null}
                          </Label>
                        </FormGroup>
                        <FormGroup>
                          <Label
                            className="ml-3 mt-2"
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
                            <Field
                              type="checkbox"
                              name={"consent"}
                              id={"consent"}
                            />{" "}
                            {t(
                              "candidates.bookmarks.personalInformation.consent"
                            )}
                            {errors.consent && touched.consent ? (
                              <div style={{ color: "red" }}>
                                {errors.consent}
                              </div>
                            ) : null}
                          </Label>
                        </FormGroup>
                      </Col>
                    )}
                    {!candidateImportFromFileUI ? (
                      <Col sm={4} md={6}>
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
                            {t(
                              "candidates.bookmarks.personalInformation.nationality"
                            )}
                          </Label>
                          <FieldArray
                            name="nationalities"
                            render={(arrayHelpers) => (
                              <Row>
                                {values.nationalities.length > 0 ? (
                                  values.nationalities.map(
                                    (nationality, index) => (
                                      <Row key={index} className={"w-100 ml-1"}>
                                        <Col sm={6} md={6}>
                                          <Field
                                            as={"select"}
                                            className={`custom-select ${
                                              index === 0 ? "" : "mt-1"
                                            }`}
                                            name={`nationalities.${index}`}
                                            id={`nationalities`}
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
                                              // background: `${
                                              //   cvData !== undefined ||
                                              //   (cvData?.RAHVUS &&
                                              //     cvData?.RAHVUS?.length > 0 &&
                                              //     cvData?.RAHVUS[index])
                                              //     ? highlightFieldColor
                                              //     : ""
                                              // }`,
                                            }}
                                          >
                                            <option>Ei ole valitud</option>
                                            {nationalities.map(
                                              (items, index) => (
                                                <option
                                                  key={index}
                                                  value={items.name}
                                                >
                                                  {items.name}
                                                </option>
                                              )
                                            )}
                                          </Field>
                                        </Col>
                                        <Col sm={3} md={3}>
                                          <Button
                                            type="button"
                                            className={`ml-1 ${
                                              index === 0 ? "" : "mt-1"
                                            }`}
                                            color={"primary"}
                                            onClick={() =>
                                              arrayHelpers.remove(index)
                                            } // remove a location from the list
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
                                            -
                                          </Button>
                                          <Button
                                            type="button"
                                            className={`ml-1 ${
                                              index === 0 ? "" : "mt-1"
                                            }`}
                                            color={"primary"}
                                            onClick={() =>
                                              arrayHelpers.insert(index, "")
                                            } // insert an empty string at a position
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
                                            +
                                          </Button>
                                        </Col>
                                      </Row>
                                    )
                                  )
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
                                    {t(
                                      "candidates.bookmarks.personalInformation.addNationality"
                                    )}
                                  </Button>
                                )}
                              </Row>
                            )}
                          />
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
                            {t(
                              "candidates.bookmarks.personalInformation.drivingLicense"
                            )}
                          </Label>
                          <FieldArray
                            name="driving_licenses"
                            render={(arrayHelpers) => (
                              <Row>
                                {values.driving_licenses.length > 0 ? (
                                  values.driving_licenses.map(
                                    (driving_licenses, index) => (
                                      <Row key={index} className={"w-100 ml-1"}>
                                        <Col sm={4} md={6}>
                                          <Field
                                            type={"text"}
                                            className={`form-control ${
                                              index === 0 ? "" : "mt-1"
                                            }`}
                                            required
                                            name={`driving_licenses.${index}.level`}
                                            id={`driving-licenses.${index}.level`}
                                            onBlur={(e) => {
                                              handleDrivingLicenseBlur(e);
                                              handleClickedBlurSearchKeywords(
                                                e,
                                                cvData,
                                                setClickedSearchKeywords
                                              );
                                            }}
                                            onClick={(e) =>
                                              handleClickedSearchKeywords(
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
                                                highlightDrivingLicenseField &&
                                                highlightDrivingLicenseField.length >
                                                  0 &&
                                                highlightDrivingLicenseField[
                                                  index
                                                ]
                                                  ? highlightFieldColor
                                                  : whiteColor
                                              }`,
                                            }}
                                          />
                                          {errors.driving_licenses &&
                                          errors.driving_licenses[index] &&
                                          errors.driving_licenses[index]
                                            .level &&
                                          touched.driving_licenses &&
                                          touched.driving_licenses[index] &&
                                          touched.driving_licenses[index]
                                            .level ? (
                                            <div style={{ color: "red" }}>
                                              {
                                                errors.driving_licenses[index]
                                                  .level
                                              }
                                            </div>
                                          ) : null}
                                        </Col>
                                        <Col sm={3} md={3}>
                                          <Button
                                            type="button"
                                            className={`ml-1 ${
                                              index === 0 ? "" : "mt-1"
                                            }`}
                                            color={"primary"}
                                            onClick={(e) => {
                                              arrayHelpers.remove(index);
                                              handleRemoveLicense(index);
                                            }} // remove a location from the list
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
                                            -
                                          </Button>
                                          <Button
                                            type="button"
                                            className={`ml-1 ${
                                              index === 0 ? "" : "mt-1"
                                            }`}
                                            color={"primary"}
                                            onClick={() =>
                                              arrayHelpers.insert(index + 1, "")
                                            } // insert an empty string at a position
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
                                            +
                                          </Button>
                                        </Col>
                                      </Row>
                                    )
                                  )
                                ) : (
                                  <Button
                                    type="button"
                                    color={"primary"}
                                    className={`mb-2 mt-2`}
                                    onClick={() => {
                                      arrayHelpers.push({
                                        level: "",
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
                                      "candidates.bookmarks.personalInformation.addDrivingLicense"
                                    )}
                                  </Button>
                                )}
                              </Row>
                            )}
                          />
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
                            {t(
                              "candidates.bookmarks.personalInformation.recommendation"
                            )}
                          </Label>
                          <FieldArray
                            name="recommendations"
                            render={(arrayHelpers) => (
                              <Row>
                                {values.recommendations.length > 0 ? (
                                  values.recommendations.map(
                                    (recommendations, index) => (
                                      <Row key={index} className={"w-100 ml-1"}>
                                        <Col sm={6} md={6}>
                                          <Field
                                            type={"text"}
                                            className={`form-control ${
                                              index === 0 ? "" : "mt-1"
                                            }`}
                                            required
                                            name={`recommendations.${index}`}
                                            id={`recommendations-id.${index}`}
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
                                                highlightRecommendationsField &&
                                                highlightRecommendationsField.length >
                                                  0 &&
                                                highlightRecommendationsField[
                                                  index
                                                ]
                                                  ? highlightFieldColor
                                                  : whiteColor
                                              }`,
                                            }}
                                          />
                                          {errors.recommendations &&
                                          errors.recommendations[index] &&
                                          touched.recommendations &&
                                          touched.recommendations[index] ? (
                                            <div style={{ color: "red" }}>
                                              {errors.recommendations[index]}
                                            </div>
                                          ) : null}
                                        </Col>
                                        <Col sm={3} md={3}>
                                          <Button
                                            type="button"
                                            className={`ml-1 ${
                                              index === 0 ? "" : "mt-1"
                                            }`}
                                            color={"primary"}
                                            onClick={() =>
                                              arrayHelpers.remove(index)
                                            } // remove a location from the list
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
                                            -
                                          </Button>
                                          <Button
                                            type="button"
                                            className={`ml-1 ${
                                              index === 0 ? "" : "mt-1"
                                            }`}
                                            color={"primary"}
                                            onClick={() =>
                                              arrayHelpers.insert(index + 1, "")
                                            } // insert an empty string at a position
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
                                            +
                                          </Button>
                                        </Col>
                                      </Row>
                                    )
                                  )
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
                                    {t(
                                      "candidates.bookmarks.personalInformation.addRecommendations"
                                    )}
                                  </Button>
                                )}
                              </Row>
                            )}
                          />
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
                            {t(
                              "candidates.bookmarks.personalInformation.keyword"
                            )}
                          </Label>
                          <FieldArray
                            name="keywords"
                            render={(arrayHelpers) => (
                              <Row>
                                {values.keywords.length > 0 ? (
                                  values.keywords.map((keywords, index) => (
                                    <Row key={index} className={"w-100 ml-1"}>
                                      <Col sm={6} md={6}>
                                        <Field
                                          type={"text"}
                                          className={`form-control ${
                                            index === 0 ? "" : "mt-1"
                                          }`}
                                          name={`keywords.${index}`}
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
                                            // background: `${
                                            //   cvData !== undefined ||
                                            //   (cvData?.MÄRKSÕNAD &&
                                            //     cvData?.MÄRKSÕNAD?.length > 0 &&
                                            //     cvData?.MÄRKSÕNAD[index])
                                            //     ? highlightFieldColor
                                            //     : ""
                                            // }`,
                                          }}
                                        />
                                        {errors.keywords &&
                                        errors.keywords[index] &&
                                        touched.keywords &&
                                        touched.keywords[index] ? (
                                          <div style={{ color: "red" }}>
                                            {errors.keywords[index]}
                                          </div>
                                        ) : null}
                                      </Col>
                                      <Col sm={3} md={3}>
                                        <Button
                                          type="button"
                                          className={`ml-1 ${
                                            index === 0 ? "" : "mt-1"
                                          }`}
                                          color={"primary"}
                                          onClick={() =>
                                            arrayHelpers.remove(index)
                                          } // remove a location from the list
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
                                          -
                                        </Button>
                                        <Button
                                          type="button"
                                          className={`ml-1 ${
                                            index === 0 ? "" : "mt-1"
                                          }`}
                                          color={"primary"}
                                          onClick={() =>
                                            arrayHelpers.insert(index + 1, "")
                                          } // insert an empty string at a position
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
                                          +
                                        </Button>
                                      </Col>
                                    </Row>
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
                                    {t(
                                      "candidates.bookmarks.personalInformation.addKeyword"
                                    )}
                                  </Button>
                                )}
                              </Row>
                            )}
                          />
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
                            {t("candidates.bookmarks.personalInformation.tag")}
                          </Label>
                          <Row>
                            <Col sm={"12"} md={"12"}>
                              <TagsInput
                                value={tags}
                                onChange={setTags}
                                name="tags"
                                placeHolder="Sisesta võtmesõna"
                              />
                            </Col>
                          </Row>
                        </FormGroup>
                      </Col>
                    ) : null}

                    {!candidateImportFromFileUI ? null : (
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
                            {t(
                              "candidates.bookmarks.personalInformation.source"
                            )}
                          </Label>
                          <Field
                            type="text"
                            name="source"
                            id="source"
                            className={"form-control"}
                            placeholder="cvkeskus.ee, facebook, google..."
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
                          {errors.source && touched.source ? (
                            <div style={{ color: "red" }}>{errors.source}</div>
                          ) : null}
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
                            {t(
                              "candidates.bookmarks.personalInformation.desiredJob"
                            )}
                          </Label>
                          <Field
                            type={"text"}
                            className={"form-control"}
                            name={"desired_job"}
                            id={"desired-job"}
                            onBlur={(e) => {
                              handleDesiredJobBlur(e);
                              handleClickedBlurSearchKeywords(
                                e,
                                cvData,
                                setClickedSearchKeywords
                              );
                            }}
                            onClick={(e) =>
                              handleClickedSearchKeywords(
                                e,
                                cvData,
                                setClickedSearchKeywords
                              )
                            }
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                              // background: fieldBgColor,
                            }}
                          />
                          {errors.desired_job && touched.desired_job ? (
                            <div style={{ color: "red" }}>
                              {errors.desired_job}
                            </div>
                          ) : null}
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
                            {t(
                              "candidates.bookmarks.personalInformation.desiredJobType"
                            )}
                          </Label>
                          <label className={"mr-3"}>
                            <Field
                              type="checkbox"
                              name="job_type"
                              id="job-type-full-time"
                              value="1"
                              onClick={(e) => handleJobTypeClick(e)}
                            />{" "}
                            {t(
                              "candidates.bookmarks.personalInformation.fullTime"
                            )}
                          </label>
                          <label
                            className={"mr-3"}
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
                            <Field
                              type="checkbox"
                              name="job_type"
                              id="job-type-part-time"
                              value="2"
                              onClick={(e) => handleJobTypeClick(e)}
                            />{" "}
                            {t(
                              "candidates.bookmarks.personalInformation.partTime"
                            )}
                          </label>
                          {errors.job_type && touched.job_type ? (
                            <div style={{ color: "red" }}>
                              {errors.job_type}
                            </div>
                          ) : null}
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
                            {t(
                              "candidates.bookmarks.personalInformation.desiredJobTime"
                            )}
                          </Label>
                          <Field
                            type={"text"}
                            className={"form-control"}
                            name={"desired_job_time"}
                            id={"desired-job-time"}
                            onBlur={(e) => {
                              handleDesiredJobTimeBlur(e);
                              handleClickedBlurSearchKeywords(
                                e,
                                cvData,
                                setClickedSearchKeywords
                              );
                            }}
                            onClick={(e) =>
                              handleClickedSearchKeywords(
                                e,
                                cvData,
                                setClickedSearchKeywords
                              )
                            }
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                              // background: fieldBgColor,
                            }}
                          />
                          {errors.desired_job_time &&
                          touched.desired_job_time ? (
                            <div style={{ color: "red" }}>
                              {errors.desired_job_time}
                            </div>
                          ) : null}
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
                            {t(
                              "candidates.bookmarks.personalInformation.desiredJobLocation"
                            )}
                          </Label>
                          <Field
                            type={"text"}
                            className={"form-control"}
                            name={"desired_job_location"}
                            id={"desired-job-location"}
                            onBlur={(e) => {
                              handleDesiredJobLocationBlur(e);
                              handleClickedBlurSearchKeywords(
                                e,
                                cvData,
                                setClickedSearchKeywords
                              );
                            }}
                            onClick={(e) =>
                              handleClickedSearchKeywords(
                                e,
                                cvData,
                                setClickedSearchKeywords
                              )
                            }
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                              // background: fieldBgColor,
                            }}
                          />
                          {errors.desired_job_location &&
                          touched.desired_job_location ? (
                            <div style={{ color: "red" }}>
                              {errors.desired_job_location}
                            </div>
                          ) : null}
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
                            {t(
                              "candidates.bookmarks.personalInformation.desiredJobSalary"
                            )}
                          </Label>
                          <Field
                            type={"number"}
                            className={"form-control"}
                            name={"desired_salary"}
                            id={"desired-job-salary"}
                            onBlur={(e) => {
                              handleDesiredJobSalaryBlur(e);
                              handleClickedBlurSearchKeywords(
                                e,
                                cvData,
                                setClickedSearchKeywords
                              );
                            }}
                            onClick={(e) =>
                              handleClickedSearchKeywords(
                                e,
                                cvData,
                                setClickedSearchKeywords
                              )
                            }
                            min={0}
                            style={{
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                              // background: fieldBgColor,
                            }}
                          />
                          {errors.desired_salary && touched.desired_salary ? (
                            <div style={{ color: "red" }}>
                              {errors.desired_salary}
                            </div>
                          ) : null}
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
                            {t("candidates.candidatePhase")}
                          </Label>
                          <Field
                            as={"select"}
                            className="custom-select"
                            name={"action_id"}
                            id={"action-id"}
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
                            <option value={""}>Ei ole valitud</option>
                            {candidateStatus.map((items, index) => (
                              <option value={items.id} key={index}>
                                {items.name}
                              </option>
                            ))}
                          </Field>
                          {errors.action_id && touched.action_id ? (
                            <div style={{ color: "red" }}>
                              {errors.action_id}
                            </div>
                          ) : null}
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
                            {t(
                              "candidates.bookmarks.personalInformation.status"
                            )}
                          </Label>
                          <Field
                            as={"select"}
                            className="custom-select"
                            name={"status"}
                            id={"action-status"}
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
                            <option value={"1"}>
                              {t(
                                "candidates.bookmarks.personalInformation.statusActive"
                              )}
                            </option>
                            <option value={"0"}>
                              {t(
                                "candidates.bookmarks.personalInformation.statusInActive"
                              )}
                            </option>
                          </Field>
                          {errors.status && touched.status ? (
                            <div style={{ color: "red" }}>{errors.status}</div>
                          ) : null}
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
                            {t(
                              "candidates.bookmarks.personalInformation.uploadPicture"
                            )}
                          </Label>
                          <Input
                            type="file"
                            id="file"
                            name="file"
                            // accept="image/png, image/jpg"
                            className={"form-control"}
                            onChange={(e) => handleImageChange(e)}
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
                            <span style={{ color: "red" }}>
                              {t("errors.invalidFileType")}
                            </span>
                          )}
                          <Alert color={"info"} className={"mt-2"}>
                            <strong>{t("alerts.allowedFormats")}:</strong> .jpg,
                            .jpeg, .png, .bmp, .gif, .svg, or .webp
                          </Alert>
                        </FormGroup>

                        <FormGroup>
                          <Label
                            className="ml-3 mt-2"
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
                            <Field
                              type="checkbox"
                              name={"newsletter"}
                              id={"newsletter"}
                            />{" "}
                            {t(
                              "candidates.bookmarks.personalInformation.newsLetter"
                            )}
                            {errors.newsletter && touched.newsletter ? (
                              <div style={{ color: "red" }}>
                                {errors.newsletter}
                              </div>
                            ) : null}
                          </Label>
                        </FormGroup>
                        <FormGroup>
                          <Label
                            className="ml-3 mt-2"
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
                            <Field
                              type="checkbox"
                              name={"consent"}
                              id={"consent"}
                            />{" "}
                            {t(
                              "candidates.bookmarks.personalInformation.consent"
                            )}
                            {errors.consent && touched.consent ? (
                              <div style={{ color: "red" }}>
                                {errors.consent}
                              </div>
                            ) : null}
                          </Label>
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
                            {t(
                              "candidates.bookmarks.personalInformation.nationality"
                            )}
                          </Label>
                          <FieldArray
                            name="nationalities"
                            render={(arrayHelpers) => (
                              <Row>
                                {values.nationalities.length > 0 ? (
                                  values.nationalities.map(
                                    (nationality, index) => (
                                      <Row key={index} className={"w-100 ml-1"}>
                                        <Col sm={6} md={6}>
                                          <Field
                                            as={"select"}
                                            className={`custom-select ${
                                              index === 0 ? "" : "mt-1"
                                            }`}
                                            name={`nationalities.${index}`}
                                            id={`nationalities`}
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
                                              // background: `${
                                              //   cvData !== undefined ||
                                              //   (cvData?.RAHVUS &&
                                              //     cvData?.RAHVUS?.length > 0 &&
                                              //     cvData?.RAHVUS[index])
                                              //     ? highlightFieldColor
                                              //     : ""
                                              // }`,
                                            }}
                                          >
                                            <option>Ei ole valitud</option>
                                            {nationalities.map(
                                              (items, index) => (
                                                <option
                                                  key={index}
                                                  value={items.name}
                                                >
                                                  {items.name}
                                                </option>
                                              )
                                            )}
                                          </Field>
                                        </Col>
                                        <Col sm={3} md={3}>
                                          <Button
                                            type="button"
                                            className={`ml-1 ${
                                              index === 0 ? "" : "mt-1"
                                            }`}
                                            color={"primary"}
                                            onClick={() =>
                                              arrayHelpers.remove(index)
                                            } // remove a location from the list
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
                                            -
                                          </Button>
                                          <Button
                                            type="button"
                                            className={`ml-1 ${
                                              index === 0 ? "" : "mt-1"
                                            }`}
                                            color={"primary"}
                                            onClick={() =>
                                              arrayHelpers.insert(index, "")
                                            } // insert an empty string at a position
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
                                            +
                                          </Button>
                                        </Col>
                                      </Row>
                                    )
                                  )
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
                                    {t(
                                      "candidates.bookmarks.personalInformation.addNationality"
                                    )}
                                  </Button>
                                )}
                              </Row>
                            )}
                          />
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
                            {t(
                              "candidates.bookmarks.personalInformation.drivingLicense"
                            )}
                          </Label>
                          <FieldArray
                            name="driving_licenses"
                            render={(arrayHelpers) => (
                              <Row>
                                {values.driving_licenses.length > 0 ? (
                                  values.driving_licenses.map(
                                    (driving_licenses, index) => (
                                      <Row key={index} className={"w-100 ml-1"}>
                                        <Col sm={4} md={6}>
                                          <Field
                                            type={"text"}
                                            className={`form-control ${
                                              index === 0 ? "" : "mt-1"
                                            }`}
                                            required
                                            name={`driving_licenses.${index}.level`}
                                            id={`driving-licenses.${index}.level`}
                                            onBlur={(e) => {
                                              handleDrivingLicenseBlur(e);
                                              handleClickedBlurSearchKeywords(
                                                e,
                                                cvData,
                                                setClickedSearchKeywords
                                              );
                                            }}
                                            onClick={(e) =>
                                              handleClickedSearchKeywords(
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
                                                highlightDrivingLicenseField &&
                                                highlightDrivingLicenseField.length >
                                                  0 &&
                                                highlightDrivingLicenseField[
                                                  index
                                                ]
                                                  ? highlightFieldColor
                                                  : whiteColor
                                              }`,
                                            }}
                                          />
                                          {errors.driving_licenses &&
                                          errors.driving_licenses[index] &&
                                          errors.driving_licenses[index]
                                            .level &&
                                          touched.driving_licenses &&
                                          touched.driving_licenses[index] &&
                                          touched.driving_licenses[index]
                                            .level ? (
                                            <div style={{ color: "red" }}>
                                              {
                                                errors.driving_licenses[index]
                                                  .level
                                              }
                                            </div>
                                          ) : null}
                                        </Col>
                                        <Col sm={3} md={3}>
                                          <Button
                                            type="button"
                                            className={`ml-1 ${
                                              index === 0 ? "" : "mt-1"
                                            }`}
                                            color={"primary"}
                                            onClick={(e) => {
                                              arrayHelpers.remove(index);
                                              handleRemoveLicense(index);
                                            }} // remove a location from the list
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
                                            -
                                          </Button>
                                          <Button
                                            type="button"
                                            className={`ml-1 ${
                                              index === 0 ? "" : "mt-1"
                                            }`}
                                            color={"primary"}
                                            onClick={() =>
                                              arrayHelpers.insert(index + 1, "")
                                            } // insert an empty string at a position
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
                                            +
                                          </Button>
                                        </Col>
                                      </Row>
                                    )
                                  )
                                ) : (
                                  <Button
                                    type="button"
                                    color={"primary"}
                                    className={`mb-2 mt-2`}
                                    onClick={() => {
                                      arrayHelpers.push({
                                        level: "",
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
                                      "candidates.bookmarks.personalInformation.addDrivingLicense"
                                    )}
                                  </Button>
                                )}
                              </Row>
                            )}
                          />
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
                            {t(
                              "candidates.bookmarks.personalInformation.recommendation"
                            )}
                          </Label>
                          <FieldArray
                            name="recommendations"
                            render={(arrayHelpers) => (
                              <Row>
                                {values.recommendations.length > 0 ? (
                                  values.recommendations.map(
                                    (recommendations, index) => (
                                      <Row key={index} className={"w-100 ml-1"}>
                                        <Col sm={6} md={6}>
                                          <Field
                                            type={"text"}
                                            className={`form-control ${
                                              index === 0 ? "" : "mt-1"
                                            }`}
                                            required
                                            name={`recommendations.${index}`}
                                            id={`recommendations-id.${index}`}
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
                                                highlightRecommendationsField &&
                                                highlightRecommendationsField.length >
                                                  0 &&
                                                highlightRecommendationsField[
                                                  index
                                                ]
                                                  ? highlightFieldColor
                                                  : whiteColor
                                              }`,
                                            }}
                                          />
                                          {errors.recommendations &&
                                          errors.recommendations[index] &&
                                          touched.recommendations &&
                                          touched.recommendations[index] ? (
                                            <div style={{ color: "red" }}>
                                              {errors.recommendations[index]}
                                            </div>
                                          ) : null}
                                        </Col>
                                        <Col sm={3} md={3}>
                                          <Button
                                            type="button"
                                            className={`ml-1 ${
                                              index === 0 ? "" : "mt-1"
                                            }`}
                                            color={"primary"}
                                            onClick={() =>
                                              arrayHelpers.remove(index)
                                            } // remove a location from the list
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
                                            -
                                          </Button>
                                          <Button
                                            type="button"
                                            className={`ml-1 ${
                                              index === 0 ? "" : "mt-1"
                                            }`}
                                            color={"primary"}
                                            onClick={() =>
                                              arrayHelpers.insert(index + 1, "")
                                            } // insert an empty string at a position
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
                                            +
                                          </Button>
                                        </Col>
                                      </Row>
                                    )
                                  )
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
                                    {t(
                                      "candidates.bookmarks.personalInformation.addRecommendations"
                                    )}
                                  </Button>
                                )}
                              </Row>
                            )}
                          />
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
                            {t(
                              "candidates.bookmarks.personalInformation.keyword"
                            )}
                          </Label>
                          <FieldArray
                            name="keywords"
                            render={(arrayHelpers) => (
                              <Row>
                                {values.keywords.length > 0 ? (
                                  values.keywords.map((keywords, index) => (
                                    <Row key={index} className={"w-100 ml-1"}>
                                      <Col sm={6} md={6}>
                                        <Field
                                          type={"text"}
                                          className={`form-control ${
                                            index === 0 ? "" : "mt-1"
                                          }`}
                                          name={`keywords.${index}`}
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
                                            // background: `${
                                            //   cvData !== undefined ||
                                            //   (cvData?.MÄRKSÕNAD &&
                                            //     cvData?.MÄRKSÕNAD?.length > 0 &&
                                            //     cvData?.MÄRKSÕNAD[index])
                                            //     ? highlightFieldColor
                                            //     : ""
                                            // }`,
                                          }}
                                        />
                                        {errors.keywords &&
                                        errors.keywords[index] &&
                                        touched.keywords &&
                                        touched.keywords[index] ? (
                                          <div style={{ color: "red" }}>
                                            {errors.keywords[index]}
                                          </div>
                                        ) : null}
                                      </Col>
                                      <Col sm={3} md={3}>
                                        <Button
                                          type="button"
                                          className={`ml-1 ${
                                            index === 0 ? "" : "mt-1"
                                          }`}
                                          color={"primary"}
                                          onClick={() =>
                                            arrayHelpers.remove(index)
                                          } // remove a location from the list
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
                                          -
                                        </Button>
                                        <Button
                                          type="button"
                                          className={`ml-1 ${
                                            index === 0 ? "" : "mt-1"
                                          }`}
                                          color={"primary"}
                                          onClick={() =>
                                            arrayHelpers.insert(index + 1, "")
                                          } // insert an empty string at a position
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
                                          +
                                        </Button>
                                      </Col>
                                    </Row>
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
                                    {t(
                                      "candidates.bookmarks.personalInformation.addKeyword"
                                    )}
                                  </Button>
                                )}
                              </Row>
                            )}
                          />
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
                            {t("candidates.bookmarks.personalInformation.tag")}
                          </Label>
                          <Row>
                            <Col sm={"12"} md={"12"}>
                              <TagsInput
                                value={tags}
                                onChange={setTags}
                                name="tags"
                                placeHolder="Enter Tag"
                              />
                            </Col>
                          </Row>
                        </FormGroup>
                      </Col>
                    )}
                  </Row>
                  <div>
                    <Button
                      type={"submit"}
                      className="btn btn-primary float-right mt-3"
                      disabled={
                        candidateSelector.isLoading || checkPersonalCode
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
                    >
                      {candidateSelector.isLoading ? (
                        <Loader />
                      ) : (
                        <>{t("buttons.save")}</>
                      )}
                    </Button>
                  </div>
                  <Persist name="candidate-personal-data-form" />
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

export default AddPersonalData;
