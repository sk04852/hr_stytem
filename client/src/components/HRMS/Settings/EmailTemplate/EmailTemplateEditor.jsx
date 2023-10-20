import React, { useState } from "react";
import CKEditor from "react-ckeditor-component";
import { Button, Col, Input, Row } from "reactstrap";
import { Formik } from "formik";
import { useDispatch, useSelector } from "react-redux";
import { Loader } from "../../../constants/loaders";
import Toast from "../../../constants/toast";
import { addEmailTemplates } from "../../../../redux/actions/emailTemplateActions";
import { useTranslation } from "react-i18next";
import { EmailTemplateSchema } from "../validations/formValidations";
import { validateHtmlTags } from "../validations/validateFuncations";
import { SubmitFormValidationErrors } from "../../../constants/errors";

const EmailTemplateEditor = (props) => {
  const { body, setBody, activeField, setActiveField } = props;

  const dispatch = useDispatch();

  const { t } = useTranslation();

  const initialValues = {
    title: "",
    template_key: "",
    subject: "",
    cc: "",
    body: "",
  };

  const [templateKeyErrorMsg, setTemplateKeyErrorMsg] = useState("");
  const [formErrors, setFormErrors] = useState({});
  const [bodyError, setBodyError] = useState(false);

  const onChange = (evt) => {
    let newContent = evt.editor.getData();
    if (newContent.length > 0) {
      setBodyError(false);
      setBody(newContent);
    } else {
      setBodyError(true);
    }
  };

  const emailTemplateSelector = useSelector((state) => state.emailTemplates);
  const settingsSelector = useSelector((state) => state.settings);

  const handleSubmit = (values) => {
    if (body.length > 0) {
      setBodyError(false);
      values["body"] = body;

      // validate
      let error = {};
      error = validateHtmlTags(values, setFormErrors);

      // submit form if there is no error
      if (Object.keys(error).length === 0 && !bodyError) {
        dispatch(addEmailTemplates(values, setTemplateKeyErrorMsg));
      }
    } else {
      setBodyError(true);
    }
  };

  return (
    <>
      {/*FORM INPUT ERRORS*/}
      {formErrors && Object.keys(formErrors).length !== 0 ? (
        <SubmitFormValidationErrors formErrors={formErrors} />
      ) : null}

      <Formik
        initialValues={initialValues}
        validationSchema={EmailTemplateSchema}
        onSubmit={(values) => handleSubmit(values)}
      >
        {(props) => (
          <form onSubmit={props.handleSubmit} name={"email_template_form"}>
            <Row>
              <Col md={12}>
                <div className={"text-right mb-3"}>
                  <Button
                    type={"submit"}
                    color={"primary"}
                    disabled={emailTemplateSelector.isLoading}
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
                    {emailTemplateSelector.isLoading ? (
                      <Loader />
                    ) : (
                      <>{t("settings.emailTemplates.saveTemplateButton")}</>
                    )}
                  </Button>
                </div>
              </Col>
            </Row>
            <div className={"d-flex flex-column mb-2"}>
              <Input
                type={"text"}
                placeholder={"Template Title *"}
                id={"title"}
                name={"title"}
                className={"form-control"}
                onChange={props.handleChange}
                onBlur={(e) => setActiveField(e.target.id)}
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
              {props.errors.title && (
                <div id="feedback" style={{ color: "red" }}>
                  {props.errors.title}
                </div>
              )}
            </div>
            <div className={"d-flex flex-column mb-2"}>
              <Input
                type={"text"}
                placeholder={"Template Key *"}
                id={"template_key"}
                name={"template_key"}
                className={"form-control"}
                onChange={props.handleChange}
                onBlur={(e) => setActiveField(e.target.id)}
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
              {props.errors.template_key && (
                <div id="feedback" style={{ color: "red" }}>
                  {props.errors.template_key}
                </div>
              )}
            </div>
            <CKEditor
              activeClass="p10"
              content={body}
              id={"body"}
              name={"body"}
              events={{
                blur: (e) => {
                  setActiveField("cke_1");
                },
                change: onChange,
              }}
              config={{
                autoParagraph: false,
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
            />
            {bodyError && (
              <span style={{ color: "red" }}>
                {t("formValidationMessages.required")}
              </span>
            )}
          </form>
        )}
      </Formik>
      <Toast />
    </>
  );
};

export default EmailTemplateEditor;
