import React, { useEffect, useState } from "react";
import {
  Button,
  Card,
  CardBody,
  CardHeader,
  Col,
  Input,
  Label,
  Modal,
  ModalBody,
  ModalFooter,
  ModalHeader,
  Row,
  Alert,
} from "reactstrap";
import { Field, Form, Formik } from "formik";
import { DataLoader, Loader } from "../../../constants/loaders";
import Toast from "../../../constants/toast";
import { useDispatch, useSelector } from "react-redux";
import { getAllUsers } from "../../../../redux/actions/usersAction";
import {
  addCandidateAgreement,
  deleteCandidateAgreement,
  getCandidateAgreement,
} from "../../../../redux/actions/candidatesAction";
import {
  FormValidationErrors,
  SubmitFormValidationErrors,
} from "../../../constants/errors";
import asterik from "../../../../assets/images/asterisk.png";
import { defaultLayoutPlugin } from "@react-pdf-viewer/default-layout";
// import { Viewer, Worker } from "@react-pdf-viewer/core";
import { AiOutlineLeft, AiOutlineRight } from "react-icons/ai";
import { useTranslation } from "react-i18next";
import { AgreementsValidationSchema } from "../validations/formValidation";
import { validateHtmlTags } from "../validations/validateHtmlTags";
import PdfViewer from "./PdfViewer";

const Agreements = ({ candidateCvID, candidateEmail, viewPdfFile }) => {
  const dispatch = useDispatch();

  const { t } = useTranslation();

  const initialValues = {
    candidatecv_id: candidateCvID,
    user_pr_id: "",
    files: [],
  };

  const [formValues, setFormValues] = useState(initialValues);
  const [modal, setModal] = useState(false);
  const [users, setUsers] = useState([]);
  const [documentFiles, setDocumentFiles] = useState({});
  const [agreements, setAgreements] = useState([]);
  const [candidateImportFromFileUI, setCandidateImportFromFileUI] =
    useState(false);

  // ERRORS STATE
  const [errors, setErrors] = useState({});
  const [formErrors, setFormErrors] = useState({});

  const [fileTypeError, setFileTypeError] = useState(false);

  const defaultLayoutPluginInstance = defaultLayoutPlugin();

  const toggleModal = () => {
    setModal(!modal);
  };

  const candidateSelector = useSelector((state) => state.candidates);
  const settingsSelector = useSelector((state) => state.settings);

  const fetchUsers = async () => {
    try {
      const response = await dispatch(getAllUsers());
      setUsers(response.payload.data.Users.data);
    } catch (error) {
      throw error;
    }
  };

  const fetchAgreements = async () => {
    try {
      const response = await dispatch(getCandidateAgreement(candidateCvID));
    } catch (error) {
      throw error;
    }
  };

  useEffect(() => {
    fetchUsers();
    fetchAgreements();
  }, []);

  useEffect(() => {
    if (candidateSelector.agreements_data) {
      setAgreements(candidateSelector.agreements_data.CandidateCVAgreements);
    }
  }, [candidateSelector.agreements_data]);

  useEffect(() => {
    if (formErrors?.length > 0) {
      document.getElementById("error-messages").scrollIntoView();
    }
  }, [formErrors]);

  const handleFileChange = (e) => {
    let files = e.target.files;
    for (const key in files) {
      if (Object.hasOwnProperty.call(files, key)) {
        const element = files[key];
        if (element.type === "application/pdf") {
          setFileTypeError(false);
          setDocumentFiles(e.target.files);
        } else {
          setFileTypeError(true);
        }
      }
    }
  };

  const handleSubmit = (values) => {
    // validate
    let error = {};
    error = validateHtmlTags(values, setFormErrors);

    // form submit if there is no error
    if (Object.keys(error).length === 0 && fileTypeError === false) {
      setFormErrors({});
      const formData = new FormData();
      formData.append("candidatecv_id", values.candidatecv_id);
      formData.append("user_pr_id", values.user_pr_id);
      formData.append("files[]", documentFiles[0]);

      dispatch(
        addCandidateAgreement(formData, candidateCvID, setModal, setErrors)
      );
    }
  };

  const handleDelete = (e, id) => {
    const confirm = window.confirm("Oled kindel?");
    if (confirm === true) {
      dispatch(deleteCandidateAgreement(id, candidateCvID));
    }
  };

  const renderAddAgreementModal = () => {
    return (
      <Modal isOpen={modal} toggle={toggleModal}>
        <ModalHeader
          toggle={toggleModal}
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
          {t(
            "candidates.bookmarks.agreements.addAContractModal.addAContractHeading"
          )}
        </ModalHeader>
        <Formik
          initialValues={formValues}
          validationSchema={AgreementsValidationSchema}
          onSubmit={(values) => handleSubmit(values)}
          children={({ errors, touched }) => (
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
                <ModalBody>
                  {/*ERRORS*/}
                  {errors && Object.keys(errors).length !== 0 ? (
                    <FormValidationErrors errors={errors} />
                  ) : null}

                  {/*FORM INPUT ERRORS*/}
                  {formErrors && Object.keys(formErrors).length !== 0 ? (
                    <SubmitFormValidationErrors formErrors={formErrors} />
                  ) : null}

                  <Row>
                    <Col sm={12} md={12} lg={12}>
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
                          "candidates.bookmarks.agreements.addAContractModal.recipient"
                        )}
                      </Label>
                      <Field
                        type={"text"}
                        className={"form-control"}
                        name={"recipient"}
                        value={candidateEmail ? candidateEmail : ""}
                        readOnly
                      />
                    </Col>
                  </Row>
                  <br />
                  <Row>
                    <Col sm={12} md={12} lg={12}>
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
                          "candidates.bookmarks.agreements.addAContractModal.selectAUser"
                        )}{" "}
                        <img
                          src={asterik}
                          height={10}
                          width={10}
                          className="mt-n2"
                        />
                      </Label>
                      <Field
                        as={"select"}
                        name={"user_pr_id"}
                        className={"custom-select"}
                        required
                      >
                        <option value={""}>Ei ole valitud</option>
                        {users.map((item, index) => (
                          <option key={index} value={item.id}>
                            {item.name}
                          </option>
                        ))}
                      </Field>
                      {errors.user_pr_id && touched.user_pr_id && (
                        <span style={{ color: "red" }}>
                          {errors.user_pr_id}
                        </span>
                      )}
                    </Col>
                  </Row>
                  <br />
                  <Row>
                    <Col sm={12} md={12} lg={12}>
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
                          "candidates.bookmarks.agreements.addAContractModal.uploadFileButton"
                        )}{" "}
                        <img
                          src={asterik}
                          height={10}
                          width={10}
                          className="mt-n2"
                        />
                      </Label>
                      <Input
                        type={"file"}
                        name={"files"}
                        onChange={(e) => handleFileChange(e)}
                        required
                      />
                    </Col>
                    {fileTypeError && (
                      <span style={{ color: "red" }}>
                        {t("errors.invalidFileType")}
                      </span>
                    )}
                    <Alert color={"info"} className={"mt-2 w-100"}>
                      <strong>{t("alerts.allowedFormats")}:</strong> .pdf
                    </Alert>
                  </Row>
                </ModalBody>
                <ModalFooter>
                  <Button
                    type={"submit"}
                    color={"primary"}
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
                      <>{t("buttons.submit")}</>
                    )}
                  </Button>
                  <Button
                    type={"button"}
                    color={"danger"}
                    onClick={toggleModal}
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
                    {t("buttons.close")}
                  </Button>
                </ModalFooter>
              </fieldset>
            </Form>
          )}
        />
      </Modal>
    );
  };

  return (
    <>
      {renderAddAgreementModal()}
      <Card>
        <CardHeader className="float-right">
          <h1>{t("candidates.bookmarks.agreementBookmark")}</h1>
        </CardHeader>

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
          {agreements !== undefined &&
          agreements[0]?.files &&
          agreements[0].files.length > 0 ? (
            ""
          ) : (
            <Row>
              <Col
                sm={candidateImportFromFileUI ? 6 : 12}
                md={candidateImportFromFileUI ? 6 : 12}
                ld={candidateImportFromFileUI ? 6 : 12}
                className={"text-right"}
              >
                <Button
                  type={"button"}
                  color={"primary"}
                  onClick={() => setModal(true)}
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
                  <i className="fe fe-plus mr-2" />
                  {t("candidates.bookmarks.agreements.addAContractButton")}
                </Button>
              </Col>
              {candidateImportFromFileUI ? (
                <>
                  {viewPdfFile ? (
                    <PdfViewer
                      cvFile={""}
                      cvData={""}
                      searchKeywords={""}
                      clickedSearchKeywords={""}
                    />
                  ) : null}
                </>
              ) : null}
            </Row>
          )}
          <Row>
            <Col sm={8} md={8}>
              {agreements !== undefined ? (
                <div className="table-responsive">
                  <table
                    id={"candidate-table"}
                    className="table table-hover table-striped table-vcenter text-nowrap mb-0 table-sortable"
                  >
                    <thead>
                      <tr>
                        <th
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
                          {t("candidates.bookmarks.agreements.agreementFile")}
                        </th>
                        <th
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
                          {t("candidates.bookmarks.agreements.agreementStatus")}
                        </th>
                        <th
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
                          {t("actions")}
                        </th>
                      </tr>
                    </thead>
                    <tbody>
                      {agreements ? (
                        agreements.map((item, index) => (
                          <tr key={index}>
                            <td
                              style={{
                                fontSize: `${
                                  settingsSelector.FontSize === "Large"
                                    ? "large"
                                    : settingsSelector.FontSize ===
                                      "Extra Large"
                                    ? "x-large"
                                    : "1rem"
                                }`,
                              }}
                            >
                              {item.files[0] === undefined
                                ? " "
                                : item.files[0].file_name}
                            </td>
                            <td
                              style={{
                                fontSize: `${
                                  settingsSelector.FontSize === "Large"
                                    ? "large"
                                    : settingsSelector.FontSize ===
                                      "Extra Large"
                                    ? "x-large"
                                    : "1rem"
                                }`,
                              }}
                            >
                              {item.agreementStatus.status}
                            </td>
                            <td>
                              <button
                                type="button"
                                className="btn btn-icon btn-sm js-sweetalert"
                                title="Delete"
                                data-type="confirm"
                                onClick={(e) => handleDelete(e, item.id)}
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
                                <i className="fa fa-trash-o text-danger" />
                              </button>
                            </td>
                          </tr>
                        ))
                      ) : (
                        <DataLoader />
                      )}
                    </tbody>
                  </table>
                </div>
              ) : null}
            </Col>
          </Row>
        </CardBody>
      </Card>
      <Toast />
    </>
  );
};

export default Agreements;
