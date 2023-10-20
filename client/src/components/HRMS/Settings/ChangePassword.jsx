import React, { useState } from "react";
import "../Candidates/candidate.css";
import { Card, CardBody, CardHeader, Col, Label, Row } from "reactstrap";
import { Formik, Form, Field } from "formik";
import { useDispatch, useSelector } from "react-redux";
import { changePassword } from "../../../redux/actions/usersAction";
import { useHistory } from "react-router-dom";
import { Loader } from "../../constants/loaders";
import Toast from "../../constants/toast";
import asterik from "../../../assets/images/asterisk.png";
import { useTranslation } from "react-i18next";
import { ChangePasswordSchema } from "./validations/formValidations";
// import { validateHtmlTags } from "./validations/validateFuncations";
import { SubmitFormValidationErrors } from "../../constants/errors";

const ChangePassword = () => {
  const dispatch = useDispatch();
  const history = useHistory();

  const { t } = useTranslation();

  const changePasswordSelector = useSelector((state) => state.users);
  const settingsSelector = useSelector((state) => state.settings);

  const [formErrors, setFormErrors] = useState({});

  const handleSubmit = (values) => {
    // validate
    // let error = {};
    // error = validateHtmlTags(values, setFormErrors);
    // error.ErrorText = t("formValidationMessages.tryAnotherPassword");

    // submit form if there is no error
    // if (Object.keys(error).length === 0) {
    dispatch(changePassword(values, history));
    // }
  };

  return (
    <>
      <Card>
        <CardHeader className="float-right">
          <h1>{t("settings.changePassword.heading")}</h1>
        </CardHeader>

        {/*FORM INPUT ERRORS*/}
        {formErrors && Object.keys(formErrors).length !== 0 ? (
          <SubmitFormValidationErrors formErrors={formErrors} />
        ) : null}

        <CardBody>
          <Row>
            <Formik
              initialValues={{
                current_password: "",
                new_password: "",
                new_password_confirmation: "",
              }}
              validationSchema={ChangePasswordSchema}
              onSubmit={(values) => handleSubmit(values)}
            >
              {({ errors, touched }) => (
                <Form className={"w-100"}>
                  <fieldset disabled={changePasswordSelector.isLoading}>
                    <Col sm={6}>
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
                        {t("settings.changePassword.currentPassword")}{" "}
                        <img
                          src={asterik}
                          height={10}
                          width={10}
                          className="mt-n2"
                        />
                      </Label>
                      <Field
                        type="password"
                        id={"current-password"}
                        name="current_password"
                        className={"form-control"}
                        placeholder="Olemasolev parool"
                        // requried
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
                      {errors.current_password && touched.current_password ? (
                        <div style={{ color: "red" }}>
                          {errors.current_password}
                        </div>
                      ) : null}
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
                        {t("settings.changePassword.newPassword")}{" "}
                        <img
                          src={asterik}
                          height={10}
                          width={10}
                          className="mt-n2"
                        />
                      </Label>
                      <Field
                        type="password"
                        id={"new-password"}
                        name="new_password"
                        className={"form-control"}
                        placeholder="Uus parool"
                        // requried
                        minLength={6}
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
                      {errors.new_password && touched.new_password ? (
                        <div style={{ color: "red" }}>
                          {errors.new_password}
                        </div>
                      ) : null}
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
                        {t("settings.changePassword.confirmNewPassword")}{" "}
                        <img
                          src={asterik}
                          height={10}
                          width={10}
                          className="mt-n2"
                        />
                      </Label>
                      <Field
                        type="password"
                        id="new-password-confirmation"
                        name="new_password_confirmation"
                        className={"form-control"}
                        placeholder="Kinnita uus parool"
                        // requried
                        minLength={6}
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
                      {errors.new_password_confirmation &&
                      touched.new_password_confirmation ? (
                        <div style={{ color: "red" }}>
                          {errors.new_password_confirmation}
                        </div>
                      ) : null}
                      <div>
                        <button
                          type={"submit"}
                          className="btn btn-primary float-right mt-3"
                          disabled={changePasswordSelector.isLoading}
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
                          {changePasswordSelector.isLoading ? (
                            <Loader />
                          ) : (
                            <>{t("buttons.update")}</>
                          )}
                        </button>
                      </div>
                    </Col>
                  </fieldset>
                </Form>
              )}
            </Formik>
          </Row>
        </CardBody>
      </Card>
      <Toast />
    </>
  );
};

export default ChangePassword;
