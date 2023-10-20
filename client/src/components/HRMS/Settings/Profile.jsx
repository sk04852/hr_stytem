import React, { useEffect, useState } from "react";
import {
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
import { Formik, Form, Field, ErrorMessage } from "formik";
import "../Candidates/candidate.css";
import { useDispatch, useSelector } from "react-redux";
import { useHistory } from "react-router-dom";
import { updateUserProfile } from "../../../redux/actions/usersAction";
import { Loader } from "../../constants/loaders";
import Toast from "../../constants/toast";
import AvatarImage from "../../../assets/images/sm/avatar2.jpg";
import AchievementBadge from "../../../assets/images/sm/achievement-badge.png";
import {
  FormValidationErrors,
  SubmitFormValidationErrors,
} from "../../constants/errors";
import asterik from "../../../assets/images/asterisk.png";
import { useTranslation } from "react-i18next";
import { UserProfileSchema } from "./validations/formValidations";
import { SUPPORTED_USERS_FILES_FORMATS } from "../../Shared/SupportedFilesExtenstions";
import { Image } from "antd";
import { validateHtmlTags } from "./validations/validateFuncations";

const Profile = ({ userProfileData, timezone, userRoles }) => {
  const dispatch = useDispatch();
  const history = useHistory();

  const { t } = useTranslation();

  const userProfileSelector = useSelector((state) => state.users);

  const initialValues = {
    name: "",
    phone: "",
    email: "",
    job_title: "",
    location: "",
    timezone_id: 1,
    photo: "",
  };

  const [editProfile, setEditProfile] = useState(false);
  const [userProfile, setUserProfile] = useState("");
  const [formValues, setFormValues] = useState(initialValues);
  const [file, setFile] = useState("");
  const [userRole, setUserRole] = useState("");

  // ERRORS STATE
  const [errors, setErrors] = useState({});
  const [formErrors, setFormErrors] = useState({});
  const [fileTypeError, setFileTypeError] = useState(false);

  const editRecord = () => {
    setEditProfile(!editProfile);
    setFileTypeError(false);
    setFormValues({
      ...formValues,
      name: userProfile.name,
      phone: userProfile.phone,
      email: userProfile.email,
      job_title: userProfile.job_title,
      location: userProfile.location === null ? "" : userProfile.location,
      timezone_id:
        userProfile.timezone_id === null ? "" : userProfile.timezone_id,
    });
  };

  useEffect(() => {
    if (userProfileData) {
      setUserProfile(userProfileData);
      setUserRole(Object.assign({}, userProfileData.roles[0]));
    }
  }, [userProfileData]);

  const handleUpdate = (values) => {
    // validate
    let error = {};
    error = validateHtmlTags(values, setFormErrors);

    // submit form if there is no error
    if (Object.keys(error).length === 0 && !fileTypeError) {
      if (file) {
        values["photo"] = file;
      } else {
        delete values["photo"];
      }
      dispatch(updateUserProfile(values, setEditProfile, setErrors));
    }
  };

  const settingsSelector = useSelector((state) => state.settings);

  const handleFileChange = (e) => {
    let file = e.target.files[0];
    if (SUPPORTED_USERS_FILES_FORMATS.includes(file.type)) {
      setFileTypeError(false);
      setFile(e.target.files[0]);
    } else {
      setFileTypeError(true);
    }
  };

  return (
    <>
      <Card>
        <CardHeader className="float-right">
          <h1>{t("settings.personalData.heading")}</h1>
          <div className="pointer" onClick={editRecord}>
            <i className="icon-pencil ml-3 "></i>
          </div>
        </CardHeader>

        {/*ERRORS*/}
        {errors && Object.keys(errors).length !== 0 ? (
          <FormValidationErrors errors={errors} />
        ) : null}

        {/*FORM INPUT ERRORS*/}
        {formErrors && Object.keys(formErrors).length !== 0 ? (
          <SubmitFormValidationErrors formErrors={formErrors} />
        ) : null}

        <CardBody>
          {!editProfile ? (
            <Container fluid={true}>
              <Row>
                <Col sm={4} className={"text-center"}>
                  <Image
                    // className="rounded mr-3"
                    src={userProfile.photo ?? AvatarImage}
                    alt="user image"
                    width={170}
                    height={170}
                    style={{ position: "relative", borderRadius: "100%" }}
                  />
                  {userProfile?.recruits >= 2 && (
                    <img
                      src={AchievementBadge}
                      style={{
                        position: "absolute",
                        left: "52px",
                        top: "40px",
                      }}
                      width={30}
                      height={30}
                    />
                  )}
                  <div className="media-body mt-3">
                    <h5 className="m-0">{userProfile.name}</h5>
                    <p className="text-muted mb-0">{userRole.name}</p>
                  </div>
                </Col>
                <Col sm={8} md={8} lg={8} className={"pl-5"}>
                  <h4>{t("settings.personalData.about")}</h4>
                  <Row>
                    <Col sm={8} md={8}>
                      <small className="text-muted">
                        {t("settings.personalData.email")}
                      </small>
                      <p className="mb-0">{userProfile.email}</p>
                    </Col>
                    <Col sm={8} md={8}>
                      <small className="text-muted">
                        {t("settings.personalData.phone")}
                      </small>
                      <p className="mb-0">{userProfile.phone}</p>
                    </Col>
                    <Col sm={8} md={8}>
                      <small className="text-muted">
                        {t("settings.personalData.location")}
                      </small>
                      <p className="mb-0">{userProfile.location}</p>
                    </Col>
                    <Col sm={8} md={8}>
                      <small className="text-muted">
                        {t("settings.personalData.timeZone")}
                      </small>
                      <p className="mb-0">{userProfile.timezone_name}</p>
                    </Col>
                  </Row>
                </Col>
                {/* <Col lg={4} md={12} sm={12}>
                  <ul className="list-group mb-3">
                    <li className="list-group-item">
                      <div className="media mb-0">
                        <img
                          style={{ position: "relative", borderRadius: "100%" }}
                          className="rounded mr-3"
                          src={userProfile.photo ?? AvatarImage}
                          alt="user image"
                          width={50}
                          height={50}
                        />
                        {userProfile?.recruits >= 2 && (
                          <img
                            src={AchievementBadge}
                            style={{
                              position: "absolute",
                              left: "52px",
                              top: "40px",
                            }}
                            width={30}
                            height={30}
                          />
                        )}
                        <div className="media-body pl-3">
                          <h5 className="m-0">{userProfile.name}</h5>
                          <p className="text-muted mb-0">{userRole.name}</p>
                        </div>
                      </div>
                    </li>
                    <li className="list-group-item">
                      <small className="text-muted">
                        {t("settings.personalData.email")}
                      </small>
                      <p className="mb-0">{userProfile.email}</p>
                    </li>
                    <li className="list-group-item">
                      <small className="text-muted">
                        {t("settings.personalData.phone")}
                      </small>
                      <p className="mb-0">{userProfile.phone}</p>
                    </li>
                    <li className="list-group-item">
                      <small className="text-muted">
                        {t("settings.personalData.location")}
                      </small>
                      <p className="mb-0">{userProfile.location}</p>
                    </li>
                    <li className="list-group-item">
                      <small className="text-muted">
                        {t("settings.personalData.timeZone")}
                      </small>
                      <p className="mb-0">{userProfile.timezone_name}</p>
                    </li>
                  </ul>
                </Col> */}
              </Row>
            </Container>
          ) : (
            <Formik
              initialValues={formValues}
              validationSchema={UserProfileSchema}
              onSubmit={(values) => handleUpdate(values)}
            >
              {({ errors, touched }) => (
                <Form
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
                  <fieldset disabled={userProfileSelector.isLoading}>
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
                            {t("settings.personalData.name")}{" "}
                            <img
                              src={asterik}
                              height={10}
                              width={10}
                              className="mt-n2"
                            />
                          </Label>
                          <Field
                            type="text"
                            name="name"
                            id="name"
                            className={"form-control"}
                            placeholder="Name"
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
                          <ErrorMessage name={`name`}>
                            {(msg) => (
                              <span style={{ color: "red" }}>{msg}</span>
                            )}
                          </ErrorMessage>
                        </FormGroup>
                      </Col>
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
                            {t("settings.personalData.phone")}{" "}
                            <img
                              src={asterik}
                              height={10}
                              width={10}
                              className="mt-n2"
                            />
                          </Label>
                          <Field
                            type="text"
                            name="phone"
                            id="phone"
                            className={"form-control"}
                            placeholder="Phone"
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
                          <ErrorMessage name={`phone`}>
                            {(msg) => (
                              <span style={{ color: "red" }}>{msg}</span>
                            )}
                          </ErrorMessage>
                        </FormGroup>
                      </Col>
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
                            {t("settings.personalData.email")}{" "}
                            <img
                              src={asterik}
                              height={10}
                              width={10}
                              className="mt-n2"
                            />
                          </Label>
                          <Field
                            type="text"
                            name="email"
                            id="email"
                            className={"form-control"}
                            placeholder="Email"
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
                          <ErrorMessage name={`email`}>
                            {(msg) => (
                              <span style={{ color: "red" }}>{msg}</span>
                            )}
                          </ErrorMessage>
                        </FormGroup>
                      </Col>
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
                            {t("settings.personalData.userRole")}
                          </Label>
                          <Field
                            type="text"
                            name="job_title"
                            id="job_title"
                            className={"form-control"}
                            value={userRole.name}
                            disabled={true}
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
                          <ErrorMessage name={`job_title`}>
                            {(msg) => (
                              <span style={{ color: "red" }}>{msg}</span>
                            )}
                          </ErrorMessage>
                        </FormGroup>
                      </Col>
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
                            {t("settings.personalData.location")}
                          </Label>
                          <Field
                            type="text"
                            name="location"
                            id="location"
                            className={"form-control"}
                            placeholder="Location"
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
                            {t("settings.personalData.timeZone")}{" "}
                            <img
                              src={asterik}
                              height={10}
                              width={10}
                              className="mt-n2"
                            />
                          </Label>
                          <Field
                            as="select"
                            name="timezone_id"
                            id="timezone_id"
                            className="custom-select"
                            placeholder="Time Zone"
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
                            <option value={""}>None Selected</option>
                            {timezone.map((items, index) => (
                              <option value={items.id} key={index}>
                                {items.name}
                              </option>
                            ))}
                          </Field>
                          <ErrorMessage name={`timezone_id`}>
                            {(msg) => (
                              <span style={{ color: "red" }}>{msg}</span>
                            )}
                          </ErrorMessage>
                        </FormGroup>
                      </Col>
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
                            {t("settings.personalData.uploadPicture")}
                          </Label>
                          <Input
                            type="file"
                            id="file"
                            name="file"
                            accept="image/png, image/jpeg, image/jpg"
                            className={"form-control"}
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
                            <strong>{t("alerts.allowedFormats")}:</strong> jpg,
                            jpeg, png
                          </Alert>
                        </FormGroup>
                      </Col>
                    </Row>
                    <Row>
                      <Col sm="12" md="12" lg="12">
                        <button
                          type={"submit"}
                          className="btn btn-primary float-right mt-3"
                          disabled={userProfileSelector.isLoading}
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
                          {userProfileSelector.isLoading ? (
                            <Loader />
                          ) : (
                            <>{t("buttons.save")}</>
                          )}
                        </button>
                      </Col>
                    </Row>
                  </fieldset>
                </Form>
              )}
            </Formik>
          )}
        </CardBody>
      </Card>
      <Toast />
    </>
  );
};

export default Profile;
