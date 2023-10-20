import React, { useEffect, useState, useRef } from "react";
import {
  Button,
  Card,
  CardBody,
  CardHeader,
  Col,
  Container,
  FormGroup,
  Input,
  Label,
  Row,
  Alert,
} from "reactstrap";
import "./candidate.css";
import { Field, FieldArray, Form, Formik } from "formik";
import { useDispatch, useSelector } from "react-redux";
import { getStatus } from "../../../redux/actions/statusActions";
import asterik from "../../../assets/images/asterisk.png";
import {
  getCandidateProfileById,
  updateCandidatePersonalInformation,
} from "../../../redux/actions/candidatesAction";
import { Loader } from "../../constants/loaders";
import Toast from "../../constants/toast";
import { useHistory, useParams } from "react-router-dom";
import { getNationalities } from "../../../redux/actions/nationalitiesActions";
import { baseURL } from "../../Shared/baseURL";
import * as moment from "moment/moment";
import { TagsInput } from "react-tag-input-component";
import { useTranslation } from "react-i18next";
import {
  FormValidationErrors,
  SubmitFormValidationErrors,
} from "../../constants/errors";
import estoniaCities from "../../Shared/EstoniaCities.json";
import ReactAvatarEditorModal from "./Modals/ReactAvatarEditorModal";
import { Image } from "antd";
import { PersonalDataSchema } from "./validations/formValidation";
import { validateCandidatePersonalDataHtmlTags } from "./validations/validateHtmlTags";

const Profile = (props) => {
  const { setTriggerUpdate } = props;
  const { id } = useParams();
  const history = useHistory();

  const { t } = useTranslation();

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
    nationality_ids: [],
    marital_status: "",
    age: "",
    tags: [],
    description: "",
    source: "",
    // mother_language: "",
    job_type: [],
    desired_job: "",
    desired_salary: "",
    desired_job_time: "",
    desired_job_location: "",
    action_id: 1,
    status: 0,
    consent: false,
    newsletter: false,
    photo: "",
    driving_licenses: [],
    recommendations: [],
    keywords: [],
    children_qty: "",
    // children_names: "",
  };

  const dispatch = useDispatch();

  const [formValues, setFormValues] = useState(initialValues);
  const [editProfile, setEditProfile] = useState(false);
  const [getAllTags, setGetAllTags] = useState([]);
  const [file, setFile] = useState("");
  const [candidateStatus, setCandidateStatus] = useState([]);
  const [nationalities, setNationalities] = useState([]);
  const [consent, setConsent] = useState(false);
  const [newsLetter, setNewsLetter] = useState(false);
  const [candidateProfile, setCandidateProfile] = useState([]);

  // ERRORS STATE
  const [errors, setErrors] = useState({});
  const [formErrors, setFormErrors] = useState({});

  // PERSONAL CODE CHECKS
  const [checkPersonalCode, setCheckPersonalCode] = useState(false);
  const [checkPersonalCodeDate, setCheckPersonalCodeDate] = useState(false);
  const [checkPersonalCodeLength, setCheckPersonalCodeLength] = useState(false);
  const [checkPersonalCodeGender, setCheckPersonalCodeGender] = useState(false);
  const [checkPersonalCodeDOB, setCheckPersonalCodeDOB] = useState(false);

  const [jobTypes, setJobTypes] = useState([]);
  const [calculatedAge, setCalculatedAge] = useState("");
  const [userPermission, setUserPermission] = useState([]);

  // TAG STATES
  const [tags, setTags] = useState([]);

  const [fileTypeError, setFileTypeError] = useState(false);

  const SUPPORTED_FORMATS = [
    "image/jpg",
    "image/jpeg",
    "image/png",
    "image/bmp",
    "image/gif",
    "image/svg+xml",
    "image/webp",
  ];

  const fetchCandidateProfile = async () => {
    try {
      const response = await dispatch(getCandidateProfileById(id));
      setCandidateProfile(response.payload.cadidatecv);
    } catch (error) {
      throw error;
    }
  };

  const fetchNationalities = async () => {
    try {
      await dispatch(getNationalities());
    } catch (error) {
      throw error;
    }
  };

  useEffect(() => {
    fetchCandidateProfile();
    fetchNationalities();
  }, [id]);

  const nationalitiesSelector = useSelector((state) => state.nationalities);
  const settingsSelector = useSelector((state) => state.settings);
  const getUserProfileSelector = useSelector(
    (state) => state.users.user_profile.data
  );

  useEffect(() => {
    if (getUserProfileSelector) {
      setUserPermission({ userPermission: getUserProfileSelector.permissions });
    }
  }, [getUserProfileSelector]);

  useEffect(() => {
    if (nationalitiesSelector.data) {
      setNationalities(nationalitiesSelector.data);
    }

    if (candidateProfile.age === null && candidateProfile.dob !== null) {
      let today = new Date();
      let birthDate = new Date(candidateProfile.dob);
      let age = today.getFullYear() - birthDate.getFullYear();
      let m = today.getMonth() - birthDate.getMonth();
      if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
        age--;
      }
      if (age > 0) {
        candidateProfile.age = age;
      } else {
        candidateProfile.age = "";
      }
    }
  }, [nationalitiesSelector.data, candidateProfile.dob, candidateProfile.age]);

  const editRecord = (id) => {
    setEditProfile(!editProfile);
    if (id) {
      let populateData = {
        first_name:
          candidateProfile.first_name === null
            ? ""
            : candidateProfile.first_name,
        last_name:
          candidateProfile.last_name === null ? "" : candidateProfile.last_name,
        gender: candidateProfile.gender === null ? "" : candidateProfile.gender,
        dob: candidateProfile.dob === null ? "" : candidateProfile.dob,
        personal_information:
          candidateProfile.personal_information === null
            ? ""
            : candidateProfile.personal_information,
        personal_code:
          candidateProfile.personal_code === null
            ? ""
            : candidateProfile.personal_code,
        phone: candidateProfile.phone === null ? "" : candidateProfile.phone,
        email: candidateProfile.email === null ? "" : candidateProfile.email,
        location:
          candidateProfile.location === null ? "" : candidateProfile.location,
        nationality_ids: [],
        marital_status:
          candidateProfile.marital_status === null
            ? ""
            : candidateProfile.marital_status,
        age: candidateProfile.age === null ? "" : candidateProfile.age,
        tags: [],
        description:
          candidateProfile.description === null
            ? ""
            : candidateProfile.description,
        source: candidateProfile.source === null ? "" : candidateProfile.source,
        job_type: [],
        desired_job:
          candidateProfile.desired_job === null
            ? ""
            : candidateProfile.desired_job,
        desired_salary:
          candidateProfile.desired_salary === null
            ? ""
            : candidateProfile.desired_salary,
        desired_job_time:
          candidateProfile.desired_job_time === null
            ? ""
            : candidateProfile.desired_job_time,
        desired_job_location:
          candidateProfile.desired_job_location === null
            ? ""
            : candidateProfile.desired_job_location,
        action_id: candidateProfile.action_id,
        status: candidateProfile.status === null ? "" : candidateProfile.status,
        consent:
          candidateProfile.consent === undefined
            ? ""
            : candidateProfile.consent === 1
            ? setConsent(true)
            : setConsent(false),
        newsletter:
          candidateProfile.newsletter === 1
            ? setNewsLetter(true)
            : setNewsLetter(false),
        photo: candidateProfile.photo,
        driving_licenses: [],
        recommendations: [],
        keywords: [],
        children_qty:
          candidateProfile.children_qty === null
            ? ""
            : candidateProfile.children_qty,
      };

      if (candidateProfile.tags.length > 0) {
        let tagsArr = [];
        candidateProfile.tags.map((tag) => {
          tagsArr.push(tag.name);
        });
        let uniqueTags = [...new Set(tagsArr)];
        setTags(uniqueTags);
      }

      let oldJobTypes = [];
      if (candidateProfile.jobTypes.length > 0) {
        for (let types of candidateProfile.jobTypes) {
          oldJobTypes.push(types.id);
        }
      }
      setJobTypes(oldJobTypes);

      if (candidateProfile.nationalities.length > 0) {
        for (let nationality of candidateProfile.nationalities) {
          if (populateData.hasOwnProperty("nationality_ids")) {
            populateData.nationality_ids.push(nationality.id);
          }
        }
      }

      if (candidateProfile.driving_licenses.length > 0) {
        for (let license of candidateProfile.driving_licenses) {
          if (populateData.hasOwnProperty("driving_licenses")) {
            populateData.driving_licenses.push({
              id: license.id,
              level: license.level,
            });
          }
        }
      }

      if (candidateProfile.recommendations.length > 0) {
        for (let recommenders of candidateProfile.recommendations) {
          if (populateData.hasOwnProperty("recommendations")) {
            populateData.recommendations.push({
              id: recommenders.id,
              recommendation: recommenders.recommendation,
            });
          }
        }
      }

      if (candidateProfile.keywords !== null) {
        let splitKeywords = candidateProfile.keywords.split(":::");
        for (let keyword of splitKeywords) {
          populateData.keywords.push(keyword);
        }
      }

      setFormValues(populateData);
    }
  };

  useEffect(() => {
    dispatch(getStatus());
  }, []);

  const getAllTagsSelector = useSelector((state) => state.tags.tags.data);
  const candidateSelector = useSelector((state) => state.candidates);
  const getStatusSelector = useSelector((state) => state.status.status.data);

  useEffect(() => {
    if (getAllTagsSelector) {
      setGetAllTags(getAllTagsSelector.Tags.data);
    }

    if (getStatusSelector) {
      setCandidateStatus(getStatusSelector.Actions.data);
    }
  }, [getAllTagsSelector, getStatusSelector]);

  const handleJobTypeCheckbox = (e) => {
    let updateList = [...jobTypes];
    let isChecked = e.target.checked;
    if (isChecked) {
      updateList = [...jobTypes, e.target.value];
    } else {
      updateList.splice(jobTypes.indexOf(e.target.value), 1);
    }
    setJobTypes(updateList);
  };

  const handleUpdate = (values) => {
    // validate
    let error = {};
    error = validateCandidatePersonalDataHtmlTags(values, tags, setFormErrors);

    // form submit if there is no error
    if (Object.keys(error).length === 0 && fileTypeError === false) {
      setFormErrors({});
      values["photo"] = file;

      if (consent === true) {
        values["consent"] = 1;
      } else {
        values["consent"] = 0;
      }

      if (newsLetter === true) {
        values["newsletter"] = 1;
      } else {
        values["newsletter"] = 0;
      }

      const formData = new FormData();
      formData.append("id", id);
      formData.append("first_name", values.first_name);
      formData.append("last_name", values.last_name);
      formData.append("gender", values.gender);

      if (values.dob !== "" && values.dob !== null) {
        formData.append("dob", values.dob);
      }

      formData.append("personal_information", values.personal_information);
      formData.append("personal_code", values.personal_code);
      formData.append("phone", values.phone);

      if (values.email !== null) {
        formData.append("email", values.email);
      }

      formData.append("location", values.location);

      if (values.nationality_ids.length > 0) {
        for (let i = 0; i < values.nationality_ids.length; i++) {
          formData.append(`nationality_ids[${i}]`, values.nationality_ids[i]);
        }
      }

      formData.append("marital_status", values.marital_status);
      formData.append(
        "age",
        values.age !== null ? values.age || calculatedAge : ""
      );

      if (tags.length > 0) {
        for (let i = 0; i < tags.length; i++) {
          formData.append("tags[]", tags[i]);
        }
      }

      formData.append("description", values.description);
      formData.append("source", values.source);
      if (jobTypes.length > 0) {
        for (let i = 0; i < jobTypes.length; i++) {
          formData.append(`job_type[${i}]`, jobTypes[i]);
        }
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
              values.driving_licenses[i][items]
            );
          });
        }
      }
      if (values.recommendations.length > 0) {
        for (let i = 0; i < values.recommendations.length; i++) {
          Object.keys(values.recommendations[i]).map((items) => {
            formData.append(
              `recommendations[${i}][${items}]`,
              values.recommendations[i][items]
            );
          });
        }
      }

      formData.append(
        `keywords`,
        values.keywords === null ? "" : values.keywords.join(":::")
      );
      formData.append("children_qty", values.children_qty);

      dispatch(
        updateCandidatePersonalInformation(
          formData,
          setEditProfile,
          id,
          setErrors,
          fetchCandidateProfile,
          setTriggerUpdate
        )
      );
    }
  };

  const handleBlur = (e) => {
    // 49409162218 PERSONAL CODE
    // 59409162218 PERSONAL CODE
    // 59217032956 PERSONAL CODE
    let personalCode = e.target.value;
    formValues.personal_code = personalCode;
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
          formValues.gender = "male";
        } else if (genderCode === "4" || genderCode === "6") {
          formValues.gender = "female";
        } else {
          formValues.gender = "none";
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

        setFormValues({
          ...formValues,
          personal_code: personalCode,
          dob: calculateDOB,
        });
      } else {
        setCheckPersonalCode(true);
        setCheckPersonalCodeDate(false);
        setCheckPersonalCodeLength(false);
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
    } else {
      setCheckPersonalCode(false);
      setCheckPersonalCodeDate(false);
      setCheckPersonalCodeLength(false);
      setCheckPersonalCodeDOB(false);
      setCheckPersonalCodeGender(false);
    }
  };

  const handleDobChange = (e) => {
    let selectedDate = e.target.value;
    formValues.dob = selectedDate;
    let ageElement = document.getElementById("age");

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
        formValues.age = age;
        setCalculatedAge(age);
      } else {
        formValues.age = "";
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

    if (jobTypeValue === "1") {
      jobTypeValue = "Täistööaeg";
    } else if (jobTypeValue === "2") {
      jobTypeValue = "Osaline tööaeg";
    }

    if (checked) {
      if (!jobTypeArr.includes(jobTypeValue)) {
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
    let licenseArr = [...tags];
    licenseArr.splice(tags.indexOf(elem), 1);
    setTags(licenseArr);
  };

  const editorRef = useRef();
  const [image, setImage] = useState(null);
  const [scale, setScale] = useState(1);
  const [isCropAvatarModalOpen, setIsCropAvatarModalOpen] = useState(false);
  const handleImageChange = (event) => {
    let files = event.target.files[0];
    if (SUPPORTED_FORMATS.includes(files.type)) {
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
        <CardHeader className="float-right w-100 justify-content-between">
          <div className={"d-flex flex-row"}>
            <h1>{t("candidates.bookmarks.personalInformationBookmark")}</h1>
            {candidateProfile && Object.keys(candidateProfile).length > 0 ? (
              <>
                {userPermission &&
                userPermission.userPermission?.includes(
                  "Can-Edit-Candidate"
                ) ? (
                  <div className="pointer pt-3" onClick={() => editRecord(id)}>
                    <i className="icon-pencil ml-3 "></i>
                  </div>
                ) : null}
              </>
            ) : null}
          </div>
          <Button
            type="button"
            className="btn btn-primary"
            onClick={() => history.push(`/hr-candidate/view/${id}/send-email`)}
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
            <i className="fa fa-envelope mr-2" />
            {t("candidates.sendEmailButton")}
          </Button>
        </CardHeader>
        <div className={"w-100 text-right pr-3"}>
          <a href={`${baseURL}candidate/cv/download/${id}`} target={"_blank"}>
            <Button
              type="button"
              className="btn btn-primary"
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
              <i className="fa fa-download mr-2" />
              {t("candidates.downloadCvButton")}
            </Button>
          </a>
        </div>
        <div className={"w-100 d-flex justify-content-end mt-3 pr-3"}>
          <Button
            type="button"
            className="btn btn-primary"
            onClick={() =>
              history.push(`/hr-candidate/view/${id}/send-link-email`)
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
            <i className="fa fa-envelope mr-2" />
            {t("candidates.sendConfirmationLinkButton")}
          </Button>
        </div>

        {/*DYNAMIC ERRORS*/}
        {errors && Object.keys(errors).length !== 0 ? (
          <FormValidationErrors errors={errors} />
        ) : null}

        {/*FORM INPUT ERRORS*/}
        {formErrors && Object.keys(formErrors).length !== 0 ? (
          <SubmitFormValidationErrors formErrors={formErrors} />
        ) : null}

        <CardBody>
          {!editProfile ? (
            <Container>
              <Row>
                <Col sm={2}>
                  <Image
                    className="rounded mr-3"
                    src={candidateProfile.photo}
                    alt="image"
                    width={80}
                    height={80}
                  />
                </Col>
                <Col sm={10} md={10} lg={10}>
                  <div>
                    <Row>
                      <Col sm={4} md={4}>
                        <strong>ID:</strong> {candidateProfile.id}
                      </Col>
                      <Col sm={4} md={4}>
                        <strong>
                          {t(
                            "candidates.bookmarks.personalInformation.firstName"
                          )}
                          :
                        </strong>{" "}
                        <br />
                        {candidateProfile.first_name}
                      </Col>
                      <Col sm={4} md={4}>
                        <strong>
                          {t(
                            "candidates.bookmarks.personalInformation.lastName"
                          )}
                          :
                        </strong>{" "}
                        <br />
                        {candidateProfile.last_name}
                      </Col>
                    </Row>
                    <Row className={"mt-3"}>
                      <Col sm={4} md={4}>
                        <strong>{t("candidates.candidateGender")}:</strong>{" "}
                        <br />
                        {candidateProfile.gender_name}
                      </Col>
                      <Col sm={4} md={4}>
                        <strong>{t("candidates.candidatePhone")}:</strong>{" "}
                        <br />
                        {candidateProfile.phone}
                      </Col>
                      <Col sm={4} md={4}>
                        <strong>
                          {t("candidates.bookmarks.personalInformation.email")}:
                        </strong>{" "}
                        <br />
                        {candidateProfile.email}
                      </Col>
                    </Row>
                    <Row className={"mt-3"}>
                      <Col sm={4} md={4}>
                        <strong>
                          {t("candidates.bookmarks.personalInformation.dob")}:
                        </strong>{" "}
                        <br />
                        {candidateProfile.dob}
                      </Col>
                      <Col sm={4} md={4}>
                        <strong>
                          {t("candidates.bookmarks.personalInformation.age")}:
                        </strong>{" "}
                        <br />
                        {candidateProfile.age}
                      </Col>
                    </Row>
                    <Row>
                      <Col sm={12} md={12}>
                        <strong>
                          {t(
                            "candidates.bookmarks.personalInformation.personalInformation"
                          )}
                          :
                        </strong>{" "}
                        <br />
                        <p className={"text-justify"}>
                          {candidateProfile.personal_information}
                        </p>
                      </Col>
                    </Row>
                    <Row className={"mt-3"}>
                      <Col sm={4} md={4}>
                        <strong>
                          {t(
                            "candidates.bookmarks.personalInformation.personalCode"
                          )}
                          :
                        </strong>{" "}
                        <br />
                        {candidateProfile.personal_code}
                      </Col>
                      <Col sm={4} md={4}>
                        <strong>
                          {t(
                            "candidates.bookmarks.personalInformation.address"
                          )}
                          :
                        </strong>{" "}
                        <br />
                        {candidateProfile.location}
                      </Col>
                      <Col sm={4} md={4}>
                        <strong>
                          {t(
                            "candidates.bookmarks.personalInformation.maritalStatus"
                          )}
                          :
                        </strong>{" "}
                        <br />
                        {candidateProfile.marital_status}
                      </Col>
                    </Row>
                    <Row className={"mt-3"}>
                      <Col sm={4} md={4}>
                        <strong>
                          {t(
                            "candidates.bookmarks.personalInformation.childrenQuantity"
                          )}
                          :
                        </strong>{" "}
                        <br />
                        {candidateProfile.children_qty}
                      </Col>
                      <Col sm={4} md={4}>
                        <strong>
                          {t(
                            "candidates.bookmarks.personalInformation.description"
                          )}
                        </strong>{" "}
                        <br />
                        {candidateProfile.description}
                      </Col>
                    </Row>
                    <Row className={"mt-3"}>
                      <Col sm={4} md={4}>
                        <strong>
                          {t("candidates.bookmarks.personalInformation.source")}
                        </strong>{" "}
                        <br />
                        {candidateProfile.source}
                      </Col>
                    </Row>
                    <Row className={"mt-3"}>
                      <Col sm={4} md={4}>
                        <strong>
                          {t(
                            "candidates.bookmarks.personalInformation.nationality"
                          )}
                        </strong>{" "}
                        <br />
                        {candidateProfile.nationalities
                          ? candidateProfile.nationalities.map(
                              (nationality, index) => (
                                <div key={index}>
                                  {index}. {nationality.name}
                                </div>
                              )
                            )
                          : ""}
                      </Col>
                      {/* <Col sm={4} md={4}>
                        <strong>
                          {t("candidates.bookmarks.educationBookmark")}
                        </strong>{" "}
                        <br />
                        {candidateProfile.education
                          ? candidateProfile.education.map((edu, index) => (
                              <div key={index}>
                                <strong>
                                  {t(
                                    "candidates.bookmarks.education.institute"
                                  )}
                                  :
                                </strong>{" "}
                                {edu.institute} |
                                <strong>
                                  {" "}
                                  {t(
                                    "candidates.bookmarks.education.speciality"
                                  )}
                                  :
                                </strong>{" "}
                                {edu.speciality} |
                                <strong>
                                  {" "}
                                  {t(
                                    "candidates.bookmarks.education.educationStartDate"
                                  )}
                                  :
                                </strong>{" "}
                                {edu.starting_date} |
                                <strong>
                                  {" "}
                                  {t(
                                    "candidates.bookmarks.education.educationEndDate"
                                  )}
                                  :
                                </strong>{" "}
                                {edu.ending_date === null ? (
                                  <>
                                    {t("candidates.bookmarks.education.until")}
                                  </>
                                ) : (
                                  edu.ending_date
                                )}
                              </div>
                            ))
                          : ""}
                      </Col> */}
                    </Row>
                    <Row className={"mt-3"}>
                      <Col sm={4} md={4}>
                        <strong>
                          {t(
                            "candidates.bookmarks.personalInformation.desiredJob"
                          )}
                        </strong>
                        <br />
                        {candidateProfile.desired_job}
                      </Col>
                      <Col sm={3} md={4}>
                        <strong>
                          {t(
                            "candidates.bookmarks.personalInformation.desiredJobType"
                          )}
                        </strong>{" "}
                        <br />
                        {candidateProfile.jobTypes
                          ? candidateProfile.jobTypes.map((item, index) => (
                              <div key={index}>
                                {item.type}
                                <br />
                              </div>
                            ))
                          : null}
                      </Col>
                      <Col sm={4} md={4}>
                        <strong>
                          {t(
                            "candidates.bookmarks.personalInformation.desiredJobTime"
                          )}
                        </strong>
                        <br />
                        {candidateProfile.desired_job_time}
                      </Col>
                    </Row>
                    <Row className={"mt-3"}>
                      <Col sm={4} md={4}>
                        <strong>
                          {t(
                            "candidates.bookmarks.personalInformation.desiredJobSalary"
                          )}
                        </strong>
                        <br />
                        {candidateProfile.desired_salary}
                      </Col>
                      <Col sm={4} md={4}>
                        <strong>
                          {t(
                            "candidates.bookmarks.personalInformation.desiredJobLocation"
                          )}
                        </strong>
                        <br />
                        {candidateProfile.desired_job_location}
                      </Col>
                    </Row>
                    <Row className={"mt-3"}>
                      <Col sm={4} md={4}>
                        <strong>
                          {t(
                            "candidates.bookmarks.personalInformation.drivingLicense"
                          )}
                        </strong>
                        <br />
                        {candidateProfile.driving_licenses
                          ? candidateProfile.driving_licenses.map(
                              (items, index) => (
                                <div key={index}>
                                  <strong>Tase: </strong> {items.level}
                                </div>
                              )
                            )
                          : ""}
                      </Col>
                      <Col sm={4} md={4}>
                        <strong>
                          {t(
                            "candidates.bookmarks.personalInformation.recommendation"
                          )}
                        </strong>
                        <br />
                        {candidateProfile.recommendations &&
                        candidateProfile.recommendations.length > 0
                          ? candidateProfile.recommendations.map(
                              (items, index) => (
                                <div key={index}>{items.recommendation}</div>
                              )
                            )
                          : ""}
                      </Col>
                    </Row>
                    <Row className={"mt-3"}>
                      <Col sm={12} md={12}>
                        <strong>
                          {t(
                            "candidates.bookmarks.personalInformation.keyword"
                          )}
                        </strong>
                        <br />
                        {candidateProfile.keywords
                          ? candidateProfile.keywords
                              .split(":::")
                              .map((keyword, index) => (
                                <div key={index}>- {keyword}</div>
                              ))
                          : ""}
                      </Col>
                    </Row>
                    <Row className={"mt-3"}>
                      <Col sm={3} md={3}>
                        <strong>
                          {t("candidates.bookmarks.personalInformation.tag")}
                        </strong>
                        <br />
                        {candidateProfile.tags &&
                        candidateProfile.tags.length > 0
                          ? candidateProfile.tags.map((tag, index) => (
                              <div key={index}>
                                <span>- {tag.name}</span>
                              </div>
                            ))
                          : ""}
                      </Col>
                      <Col sm={3} md={3}>
                        <strong>
                          {t("candidates.bookmarks.personalInformation.status")}
                        </strong>
                        <br />
                        {candidateProfile.status === 1 ? (
                          <>
                            {t(
                              "candidates.bookmarks.personalInformation.statusActive"
                            )}
                          </>
                        ) : (
                          <>
                            {t(
                              "candidates.bookmarks.personalInformation.statusInActive"
                            )}
                          </>
                        )}
                      </Col>
                    </Row>
                    <Row className={"mt-3"}>
                      <Col sm={4} md={4}>
                        <div className="form-check">
                          <input
                            className="form-check-input"
                            type="checkbox"
                            value=""
                            id="flexCheckDefault"
                            onChange={() => {}}
                            checked={candidateProfile.consent}
                          />
                          <label
                            className="form-check-label"
                            htmlFor="flexCheckDefault"
                          >
                            {t(
                              "candidates.bookmarks.personalInformation.consent"
                            )}
                          </label>
                        </div>
                      </Col>
                      <Col sm={4} md={4}>
                        <div className="form-check">
                          <input
                            className="form-check-input"
                            type="checkbox"
                            value=""
                            id="flexCheckDefault"
                            onChange={() => {}}
                            checked={candidateProfile.newsletter}
                          />
                          <label
                            className="form-check-label"
                            htmlFor="flexCheckDefault"
                          >
                            {t(
                              "candidates.bookmarks.personalInformation.newsLetter"
                            )}
                          </label>
                        </div>
                      </Col>
                    </Row>
                  </div>
                </Col>
              </Row>
            </Container>
          ) : (
            <Formik
              initialValues={formValues}
              enableReinitialize={true}
              validationSchema={PersonalDataSchema}
              onSubmit={(values) => handleUpdate(values)}
              children={({ values, errors, touched }) => (
                <Form
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
                          {errors.last_name && touched.last_name ? (
                            <div style={{ color: "red" }}>
                              {errors.last_name}
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
                            onBlur={(e) => handleGenderBlur(e)}
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
                            className={"form-control"}
                            placeholder="sünniaeg"
                            // defaultValue={formValues.dob}
                            onChange={(e) => handleDobChange(e)}
                            // onFocus={(e) => handleDobChange(e)}
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
                            maxLength={"11"}
                            onBlur={(e) => handleBlur(e)}
                            required={checkPersonalCode ? true : false}
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
                                  {t(
                                    "formValidationMessages.personalCode.limit"
                                  )}
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
                              "candidates.bookmarks.personalInformation.email"
                            )}{" "}
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
                            onBlur={(e) => handleAddressBlur(e)}
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
                            }}
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
                            // value={calculatedAge || formValues.age}
                            title={"It will be calculated once you fill DOB"}
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
                              age must between 0 to 150
                            </div>
                          ) : null}
                        </FormGroup>
                      </Col>
                      <div className="col-sm-4">
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
                            placeholder="Allikas"
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
                            onBlur={(e) => handleDesiredJobBlur(e)}
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
                              "candidates.bookmarks.personalInformation.desiredJobType"
                            )}
                          </Label>
                          <label className={"ml-3 mr-3 mt-2"}>
                            <Input
                              type="checkbox"
                              name="job_type"
                              value="1"
                              defaultChecked={jobTypes.includes(1)}
                              onChange={(e) => handleJobTypeCheckbox(e)}
                              onClick={(e) => handleJobTypeClick(e)}
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
                            />{" "}
                            {t(
                              "candidates.bookmarks.personalInformation.fullTime"
                            )}
                          </label>
                          <label className={"ml-3 mt-2"}>
                            <Input
                              type="checkbox"
                              name="job_type"
                              value="2"
                              defaultChecked={jobTypes.includes(2)}
                              onChange={(e) => handleJobTypeCheckbox(e)}
                              onClick={(e) => handleJobTypeClick(e)}
                            />{" "}
                            {t(
                              "candidates.bookmarks.personalInformation.partTime"
                            )}
                          </label>
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
                            onBlur={(e) => handleDesiredJobTimeBlur(e)}
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
                            onBlur={(e) => handleDesiredJobLocationBlur(e)}
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
                            onBlur={(e) => handleDesiredJobSalaryBlur(e)}
                            min={0}
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
                            <Input
                              type="checkbox"
                              name={"newsletter"}
                              id={"newsletter"}
                              defaultChecked={newsLetter}
                              onChange={() => setNewsLetter(!newsLetter)}
                            />{" "}
                            {t(
                              "candidates.bookmarks.personalInformation.newsLetter"
                            )}
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
                            <Input
                              type="checkbox"
                              name={"consent"}
                              id={"consent"}
                              defaultChecked={consent}
                              onChange={() => setConsent(!consent)}
                            />{" "}
                            {t(
                              "candidates.bookmarks.personalInformation.consent"
                            )}
                          </Label>
                        </FormGroup>
                      </div>
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
                            name="nationality_ids"
                            render={(arrayHelpers) => (
                              <Row>
                                {values.nationality_ids.length > 0 ? (
                                  values.nationality_ids.map(
                                    (nationality, index) => (
                                      <Row key={index} className={"w-100 ml-1"}>
                                        <Col sm={6} md={6}>
                                          <Field
                                            as={"select"}
                                            className={`custom-select ${
                                              index === 0 ? "" : "mt-1"
                                            }`}
                                            name={`nationality_ids.${index}`}
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
                                            <option>
                                              Nõusolek lisada andmebaasi
                                            </option>
                                            {nationalities.map(
                                              (items, index) => (
                                                <option
                                                  key={index}
                                                  value={items.id}
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
                                    onClick={() =>
                                      arrayHelpers.push({
                                        id: "",
                                        nationality_ids: "",
                                      })
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
                                            className={"form-control"}
                                            name={`driving_licenses.${index}.level`}
                                            id={`driving-licenses.${index}.level`}
                                            onBlur={(e) =>
                                              handleDrivingLicenseBlur(e)
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
                                        <Col sm={3} md={3}>
                                          <Button
                                            type="button"
                                            className={`ml-1 ${
                                              index === 0 ? "" : "mt-1"
                                            }`}
                                            color={"primary"}
                                            onClick={() => {
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
                                    className={`mb-2 mt-2`}
                                    onClick={() => {
                                      arrayHelpers.push({
                                        id: "",
                                        level: "",
                                        // issue_date: "",
                                        // expiry_date: "",
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
                                            name={`recommendations.${index}.recommendation`}
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
                                              arrayHelpers.insert(index, {
                                                id: "",
                                                recommendation: "",
                                              })
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
                                    onClick={() =>
                                      arrayHelpers.push({
                                        id: "",
                                        recommendation: "",
                                      })
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
                                          }}
                                        />
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
                  </fieldset>
                </Form>
              )}
            />
          )}
        </CardBody>
      </Card>
      <Toast />
    </>
  );
};

export default Profile;
