import React, { useEffect, useState } from "react";
import { useHistory, useParams } from "react-router-dom";
import { useDispatch, useSelector } from "react-redux";
import {
  getEmailTemplates,
  updateEmailTemplates,
} from "../../../../redux/actions/emailTemplateActions";
import {
  Button,
  Card,
  CardBody,
  CardHeader,
  CardTitle,
  Col,
  Container,
  FormGroup,
  Input,
  Row,
} from "reactstrap";
import { Loader } from "../../../constants/loaders";
import { useFormik } from "formik";
import CKEditor from "react-ckeditor-component";
import Toast from "../../../constants/toast";
import { useTranslation } from "react-i18next";
import Wildcards from "./Wildcards";
import { validateHtmlTags } from "../validations/validateFuncations";
import { SubmitFormValidationErrors } from "../../../constants/errors";

const EditEmailTemplate = (props) => {
  const { fixNavbar } = props;
  const { id } = useParams();

  const history = useHistory();
  const dispatch = useDispatch();

  const { t } = useTranslation();

  const initialValues = {
    title: "",
    template_key: "",
    subject: "",
    cc: "",
    body: "",
  };

  const [formValues, setFormValues] = useState(initialValues);
  const [templateDataById, setTemplateDataById] = useState("");
  const [body, setBody] = useState("");
  const [activeField, setActiveField] = useState(null);
  const [formErrors, setFormErrors] = useState({});
  const [bodyError, setBodyError] = useState(false);

  useEffect(() => {
    if (templateDataById) {
      setFormValues({
        ...formValues,
        title: templateDataById.title,
        template_key: templateDataById.template_key,
        // to: templateDataById.to,
        // cc: templateDataById.cc,
      });
      setBody(templateDataById.body);
    }
  }, [templateDataById]);

  const formik = useFormik({
    initialValues: formValues,
    onSubmit: (values) => {
      if (body.length > 0) {
        setBodyError(false);
        values["title"] = formValues.title;
        values["template_key"] = formValues.template_key;
        values["body"] = body;

        // validate
        let error = {};
        error = validateHtmlTags(values, setFormErrors);

        // submit form if there is no error
        if (Object.keys(error).length === 0 && !bodyError) {
          dispatch(updateEmailTemplates(id, values));
        }
      } else {
        setBodyError(true);
      }
    },
  });

  useEffect(() => {
    const fetchEmailTemplates = async () => {
      try {
        const response = await dispatch(getEmailTemplates(1));
        if (response.payload.data) {
          let templateData = response.payload.data.find((item) => {
            return item.id === parseInt(id);
          });
          setTemplateDataById(templateData);
        }
      } catch (error) {
        throw error;
      }
    };
    fetchEmailTemplates();
  }, []);

  const emailTemplateLoadingSelector = useSelector(
    (state) => state.emailTemplates.isLoading
  );
  const settingsSelector = useSelector((state) => state.settings);

  const onChange = (evt) => {
    let newContent = evt.editor.getData();
    if (newContent.length > 0) {
      setBodyError(false);
      setBody(newContent);
    } else {
      setBodyError(true);
    }
  };

  return (
    <div className={`section-body ${fixNavbar ? "marginTop" : ""} mt-3`}>
      <Container fluid={true}>
        <Row className="clearfix">
          <Col md={12}>
            <div className={"text-right mb-3 mr-3"}>
              <Button
                type={"button"}
                color={"primary"}
                onClick={() => {
                  history.push("/jobportal-settings/3");
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
                {t("buttons.back")}
              </Button>
            </div>
          </Col>
          <Col md={8}>
            <Card>
              <CardHeader>
                <CardTitle
                  className={"font-weight-bold"}
                  style={{
                    fontSize: `${
                      settingsSelector.FontSize === "Large"
                        ? "large"
                        : settingsSelector.FontSize === "Extra Large"
                        ? "x-large"
                        : "15px"
                    }`,
                  }}
                >
                  {t("settings.emailTemplates.editTemplateHeading")}
                </CardTitle>
              </CardHeader>

              {/*FORM INPUT ERRORS*/}
              {formErrors && Object.keys(formErrors).length !== 0 ? (
                <SubmitFormValidationErrors formErrors={formErrors} />
              ) : null}

              <CardBody>
                <form onSubmit={formik.handleSubmit}>
                  <fieldset disabled={emailTemplateLoadingSelector}>
                    <Row>
                      <Col md={12}>
                        <div className={"text-right mb-3"}>
                          <Button
                            type={"submit"}
                            color={"primary"}
                            disabled={emailTemplateLoadingSelector}
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
                            {emailTemplateLoadingSelector ? (
                              <Loader />
                            ) : (
                              <>
                                {t(
                                  "settings.emailTemplates.updateTemplateButton"
                                )}
                              </>
                            )}
                          </Button>
                        </div>
                      </Col>
                    </Row>
                    <FormGroup className={"d-flex mb-2"}>
                      <Input
                        type={"text"}
                        placeholder={"Template Title *"}
                        id={"title"}
                        name={"title"}
                        className={"form-control"}
                        defaultValue={templateDataById.title}
                        required
                        onChange={(e) =>
                          setFormValues({
                            ...formValues,
                            title: e.target.value,
                          })
                        }
                        onBlur={(e) => {
                          setActiveField(e.target.id);
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
                      {/* {props.errors.title && (
                <div id="feedback" style={{ color: "red" }}>
                  {props.errors.title}
                </div>
              )} */}
                    </FormGroup>
                    <div className={"d-flex mb-2"}>
                      <Input
                        type={"text"}
                        placeholder={"Template Key *"}
                        id={"template_key"}
                        name={"template_key"}
                        className={"form-control"}
                        defaultValue={templateDataById.template_key}
                        required
                        onChange={(e) =>
                          setFormValues({
                            ...formValues,
                            template_key: e.target.value,
                          })
                        }
                        onBlur={(e) => {
                          setActiveField(e.target.id);
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
                      {/* {props.errors.template_key && (
                <div id="feedback" style={{ color: "red" }}>
                  {props.errors.template_key}
                </div>
              )} */}
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
                    />
                    {bodyError && (
                      <span style={{ color: "red" }}>
                        {t("formValidationMessages.required")}
                      </span>
                    )}
                  </fieldset>
                </form>
              </CardBody>
            </Card>
          </Col>
          <Wildcards
            activeField={activeField}
            body={body}
            setBody={setBody}
            disabledCards={false}
          />
        </Row>
      </Container>
      <Toast />
    </div>
  );
};

export default EditEmailTemplate;
