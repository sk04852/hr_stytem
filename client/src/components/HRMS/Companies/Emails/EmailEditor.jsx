import React, { useEffect, useState } from "react";
import CKEditor from "react-ckeditor-component";
import { Button, Col, FormGroup, Row, Form, Input } from "reactstrap";
import { useDispatch, useSelector } from "react-redux";
import { getEmailTemplates } from "../../../../redux/actions/emailTemplateActions";
import {
  getAllCompanyProfileById,
  getTemplateData,
} from "../../../../redux/actions/companiesActions";
import { useHistory, useLocation, useParams } from "react-router-dom";
import { useFormik } from "formik";
import { sendEmail } from "../../../../redux/actions/emailSendingActions";
import Toast from "../../../constants/toast";
import { Loader } from "../../../constants/loaders";
import { useTranslation } from "react-i18next";
import VerificationZoomLinkModal from "./Modals/VerificationZoomLinkModal";

const EmailEditor = () => {
  const { id } = useParams();
  const dispatch = useDispatch();
  const history = useHistory();
  const location = useLocation();

  const { t } = useTranslation();

  const initialValues = {
    from: "",
    to: "",
    subject: "",
    files: [],
    body: "",
  };

  const [formValues, setFormValues] = useState(initialValues);
  const [emailsTemplates, setEmailTemplates] = useState([]);
  const [templateData, setTemplateData] = useState({});
  const [body, setBody] = useState("");
  const [uploadFiles, setUploadFiles] = useState([]);
  const [checkZoomLink, setCheckZoomLink] = useState(false);
  const [verificationZoomLinkModalIsOpen, setVerificationZoomLinkModalIsOpen] =
    useState(false);

  const toggleVerificationZoomLinkModal = () => {
    setVerificationZoomLinkModalIsOpen(!verificationZoomLinkModalIsOpen);
  };

  useEffect(() => {
    const fetchEmails = async () => {
      const response = await dispatch(getEmailTemplates());
      setEmailTemplates(response.payload.data);
    };
    fetchEmails();
  }, []);

  useEffect(() => {
    const fetchCompanyData = async () => {
      try {
        const response = await dispatch(getAllCompanyProfileById(id));

        let companyEmails = response.payload.company.companyContacts[0].email;
        setFormValues({ ...formValues, to: companyEmails });
      } catch (error) {
        throw error;
      }
    };
    fetchCompanyData();
  }, []);

  const sendEmailSelector = useSelector((state) => state.sendEmail);
  const userEmailSelector = useSelector(
    (state) => state.users.user_profile.data.email
  );
  const settingsSelector = useSelector((state) => state.settings);

  const onChange = (evt) => {
    let newContent = evt.editor.getData();
    setBody(newContent);
  };

  const handleChange = async (e) => {
    const templateID = e.target.value;
    if (templateID === "none") {
      setFormValues({
        ...formValues,
        to: templateData.to,
        subject: "",
      });
      setBody("");
    } else {
      const response = await dispatch(getTemplateData(id, templateID));
      if (response !== undefined) {
        let templateData = response.payload.data.template;
        setCheckZoomLink(false);
        setTemplateData(templateData);
        setFormValues({
          ...formValues,
          to: templateData.to,
          subject: templateData.title,
        });
        setBody(templateData.body);
      } else {
        setCheckZoomLink(true);

        // verification zoom link
        setTimeout(() => {
          let confirm = window.confirm(t("confirm.verifyZoomAccount"));
          if (confirm) {
            setVerificationZoomLinkModalIsOpen(true);
          }
        }, 3000);
      }
    }
  };

  console.log(formValues);

  const formik = useFormik({
    initialValues: formValues,
    enableReinitialize: true,
    onSubmit: (values) => {
      let files = [];
      if (uploadFiles.length > 0) {
        for (let i = 0; i <= uploadFiles.length; i++) {
          files.push(uploadFiles[0][i]);
        }
      }

      formValues.from = userEmailSelector;
      formValues.files = files;
      formValues.body = body;

      const formData = new FormData();
      formData.append("from", formValues.from);
      formData.append("to", formValues.to);
      formData.append("subject", formValues.subject);
      if (files.length > 0) {
        for (const file of files) {
          formData.append("files[]", file);
        }
      }
      formData.append("body", formValues.body);
      dispatch(sendEmail(formData, id, history, location));
    },
  });

  return (
    <>
      <VerificationZoomLinkModal
        verificationZoomLinkModalIsOpen={verificationZoomLinkModalIsOpen}
        toggleVerificationZoomLinkModal={toggleVerificationZoomLinkModal}
      />
      <Form onSubmit={formik.handleSubmit}>
        <fieldset disabled={sendEmailSelector.isLoading}>
          <div className={"d-flex flex-column mb-2"}>
            <Row>
              <Col md={8}>
                <FormGroup>
                  <Input
                    type={"text"}
                    placeholder={"FROM"}
                    id={"from"}
                    name={"from"}
                    className={`form-control`}
                    value={userEmailSelector}
                    disabled={true}
                    // onChange={(e) =>
                    //   setFormValues({ ...formValues, from: e.target.value })
                    // }
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
              </Col>
              <Col md={4}>
                <FormGroup>
                  <select
                    className={"custom-select"}
                    onChange={(e) => handleChange(e)}
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
                    <option value={"none"}>Ei ole valitud</option>
                    {emailsTemplates.map((items, index) => (
                      <option key={index} value={items.id}>
                        {items.title}
                      </option>
                    ))}
                  </select>
                </FormGroup>
              </Col>
            </Row>
            <FormGroup>
              <Input
                type={"text"}
                placeholder={"Kellele"}
                id={"to"}
                name={"to"}
                className={"form-control mt-2"}
                // defaultValue={templateData.to}
                defaultValue={formValues.to}
                onChange={(e) =>
                  setFormValues({ ...formValues, to: e.target.value })
                }
                required={true}
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
              {formik.touched.to && formik.errors.to && (
                <span className={"text-red"}>{formik.errors.to}</span>
              )}
            </FormGroup>
            <FormGroup>
              <Input
                type={"text"}
                placeholder={"Teema"}
                id={"subject"}
                name={"subject"}
                className={"form-control mt-2"}
                defaultValue={formValues.subject}
                // defaultValue={templateData.title}
                onChange={(e) =>
                  setFormValues({ ...formValues, subject: e.target.value })
                }
                required={true}
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
              <Input
                type={"file"}
                id={"files"}
                name={"files"}
                className={"form-control mt-2"}
                multiple={true}
                onChange={(e) => {
                  setUploadFiles([...uploadFiles, e.target.files]);
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
            </FormGroup>
          </div>
          <CKEditor
            activeClass="p10"
            content={body}
            events={{
              // blur: onBlur,
              change: onChange,
            }}
            config={{
              autoParagraph: false,
            }}
          />
          <div className={"w-100 text-right mt-3"}>
            {checkZoomLink ? (
              <Button
                type={"button"}
                color={"primary"}
                onClick={() =>
                  window.alert(
                    "You are not logged in with zoom. Please select another template or login with zoom first and then try again."
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
                }}
              >
                {t("buttons.send")}
              </Button>
            ) : (
              <Button
                type={"submit"}
                color={"primary"}
                disabled={sendEmailSelector.isLoading}
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
                {sendEmailSelector.isLoading ? (
                  <Loader />
                ) : (
                  <>{t("buttons.send")}</>
                )}
              </Button>
            )}
          </div>
        </fieldset>
      </Form>
      <Toast />
    </>
  );
};

export default EmailEditor;
