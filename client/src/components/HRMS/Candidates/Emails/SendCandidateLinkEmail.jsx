import React, { useEffect, useState } from "react";
import {
  Button,
  Card,
  CardBody,
  CardHeader,
  Col,
  Container,
  Form,
  FormGroup,
  Input,
  Row,
} from "reactstrap";
import { NavLink, useHistory, useLocation, useParams } from "react-router-dom";
import { getEmailTemplates } from "../../../../redux/actions/emailTemplateActions";
import { useDispatch, useSelector } from "react-redux";
import { useFormik } from "formik";
import { sendEmail } from "../../../../redux/actions/emailSendingActions";
import CKEditor from "react-ckeditor-component";
import { Loader } from "../../../constants/loaders";
import Toast from "../../../constants/toast";
import { getCandidateEmailLinkData } from "../../../../redux/actions/candidatesAction";
import { getCompanyEmailLinkData } from "../../../../redux/actions/companiesActions";
import { useTranslation } from "react-i18next";

const SendCandidateLinkEmail = (props) => {
  const { fixNavbar } = props;
  const { id } = useParams();
  const dispatch = useDispatch();
  const history = useHistory();
  const location = useLocation();

  const { t } = useTranslation();

  const RenderSendEmailEditor = () => {
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

    const fetchEmails = async () => {
      const RECORDS_PER_PAGE = 100
      const response = await dispatch(getEmailTemplates(1, RECORDS_PER_PAGE));
      response.payload.data.map((item) => {
        if (item.template_key.includes("Kandidaadi_CV")) {
          setEmailTemplates([item]);
        }
      });
    };

    const fetchCandidateEmailData = async () => {
      try {
        const response = await dispatch(
          getCandidateEmailLinkData(
            id,
            13,
            setTemplateData,
            setFormValues,
            formValues,
            setBody
          )
        );
      } catch (error) {
        throw error;
      }
    };

    useEffect(() => {
      fetchEmails();
      fetchCandidateEmailData();
    }, []);

    const sendEmailSelector = useSelector((state) => state.sendEmail);
    const userEmailSelector = useSelector(
      (state) => state.users.user_profile.data.email
    );

    const onChange = (evt) => {
      let newContent = evt.editor.getData();
      setBody(newContent);
    };

    const handleChange = async (e) => {
      const templateID = e.target.value;
      const response = await dispatch(
        getCandidateEmailLinkData(
          id,
          templateID,
          setTemplateData,
          setFormValues,
          formValues,
          setBody
        )
      );
    };

    const formik = useFormik({
      initialValues: formValues,
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
        <Form onSubmit={formik.handleSubmit}>
          <fieldset disabled={sendEmailSelector.isLoading}>
            <div className={"d-flex flex-column mb-2"}>
              <Row>
                <Col md={8}>
                  <FormGroup>
                    <Input
                      type={"text"}
                      placeholder={"Kellelt"}
                      id={"from"}
                      name={"from"}
                      className={`form-control`}
                      value={userEmailSelector}
                      disabled={true}
                    />
                  </FormGroup>
                </Col>
                <Col md={4}>
                  <FormGroup>
                    <select
                      className={"custom-select"}
                      onChange={(e) => handleChange(e)}
                    >
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
                  defaultValue={formValues.to}
                  onChange={(e) =>
                    setFormValues({ ...formValues, to: e.target.value })
                  }
                  required={true}
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
                  onChange={(e) =>
                    setFormValues({ ...formValues, subject: e.target.value })
                  }
                  required={true}
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
                removePlugins: "sourcearea,link",
                removeButtons: "Link",
                extraAllowedContent: "a[*]",
              }}
            />
            <div className={"w-100 text-right mt-3"}>
              <Button
                type={"submit"}
                color={"primary"}
                disabled={sendEmailSelector.isLoading}
              >
                {sendEmailSelector.isLoading ? <Loader /> : <>{t('buttons.send')}</>}
              </Button>
            </div>
          </fieldset>
        </Form>
        <Toast />
      </>
    );
  };

  return (
    <>
      <div className={`section-body ${fixNavbar ? "marginTop" : ""} mt-3`}>
        <Container fluid={true}>
          <Row className="clearfix">
            <Col md={12}>
              <div className={"text-right mb-3 mr-3"}>
                <Button
                  type={"button"}
                  color={"primary"}
                  onClick={() => history.push(`/hr-candidate/view/${id}/1`)}
                >
                  {t("buttons.back")}
                </Button>
              </div>
            </Col>
            <Col md={12}>
              <div className="container-fluid">
                <ul className="nav nav-tabs page-header-tab">
                  <li className="nav-item">
                    <NavLink to="#" className="nav-link active">
                    {t("sendEmailsTabs.companyTab")}
                    </NavLink>
                  </li>
                </ul>
              </div>
              <Card>
                <CardHeader>
                  {/*<CardTitle className={"font-weight-bold"}>*/}
                  {/*  CONTENT | KUTSE TOOVESTLUSELE*/}
                  {/*</CardTitle>*/}
                </CardHeader>
                <CardBody>
                  <div className="summernote">
                    <RenderSendEmailEditor />
                  </div>
                </CardBody>
              </Card>
            </Col>
          </Row>
        </Container>
      </div>
    </>
  );
};

export default SendCandidateLinkEmail;
