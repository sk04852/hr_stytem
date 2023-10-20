import React, { useEffect, useState } from "react";
import {
  Alert,
  Button,
  Card,
  CardBody,
  CardHeader,
  Col,
  Input,
  Modal,
  ModalBody,
  ModalFooter,
  ModalHeader,
  Row,
} from "reactstrap";
import "./candidate.css";
import { Form, Formik } from "formik";
import { Loader } from "../../constants/loaders";
import Toast from "../../constants/toast";
import { useDispatch, useSelector } from "react-redux";
import {
  deleteUploadFilesById,
  getUploadFilesById,
  uploadFiles,
} from "../../../redux/actions/candidatesAction";
import { FormValidationErrors } from "../../constants/errors";
import { useParams } from "react-router-dom";
import { useTranslation } from "react-i18next";

const Files = ({ candidateCvId }) => {
  const { id } = useParams();
  const dispatch = useDispatch();

  const { t } = useTranslation();

  const sizes = ["Bytes", "KB", "MB", "GB", "TB"];

  const [uploadCandidateFiles, setUploadCandidateFiles] = useState([]);
  const [previewFiles, setPreviewFiles] = useState([]);
  const [recentFiles, setRecentFiles] = useState([]);
  const [modal, setModal] = useState(false);
  const [errors, setErrors] = useState({});
  const [triggerUpdate, setTriggerUpdate] = useState(null);
  const [fileTypeError, setFileTypeError] = useState(false);

  const toggleModal = () => {
    setModal(!modal);
  };

  const SUPPORTED_FORMATS = [
    "image/jpg",
    "image/jpeg",
    "image/png",
    "application/msword",
    "application/application/vnd.openxmlformats-officedocument.wordprocessingml.document",
    "application/pdf",
    "application/rtf",
    "application/vnd.ms-excel",
    "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
    "text/plain",
    "application/zip",
  ];

  const candidateLoadingSelector = useSelector(
    (state) => state.candidates.isLoading
  );
  const settingsSelector = useSelector((state) => state.settings);

  const fetchCandidateFilesByID = async () => {
    try {
      const response = await dispatch(getUploadFilesById(candidateCvId));
      if (response) {
        setPreviewFiles(response.payload.data.CandidateCVFiles);
        let recentFiles = [];
        response.payload.data.CandidateCVFiles.reverse()
          .slice(0, 3)
          .map((item) => {
            recentFiles.push(item);
          });
        setRecentFiles(recentFiles);
      }
    } catch (error) {
      throw error;
    }
  };

  useEffect(() => {
    fetchCandidateFilesByID();
  }, [triggerUpdate]);

  const handleFileChange = (e) => {
    let files = e.target.files;
    for (const key in files) {
      if (Object.hasOwnProperty.call(files, key)) {
        const element = files[key];
        if (SUPPORTED_FORMATS.includes(element.type)) {
          setFileTypeError(false);
          setUploadCandidateFiles([...uploadCandidateFiles, files]);
        } else {
          setFileTypeError(true);
        }
      }
    }
  };

  const handleUpload = (values) => {
    if (!fileTypeError) {
      values["files"] = uploadCandidateFiles;

      let file = [];
      for (let i = 0; i < values["files"][0].length; i++) {
        file.push(values["files"][0][i]);
      }
      values["files"] = file;
      dispatch(
        uploadFiles(
          values,
          setUploadCandidateFiles,
          candidateCvId,
          setModal,
          setErrors,
          fetchCandidateFilesByID,
          ""
        )
      );
    }
  };

  const handleDeleteFile = (e, id) => {
    const confirm = window.confirm("Are You Sure?");
    if (confirm === true) {
      dispatch(deleteUploadFilesById(id, candidateCvId, setTriggerUpdate));
      // fetchCandidateFilesByID();
    }
  };

  const renderAddFilesModal = () => {
    return (
      <Modal isOpen={modal} toggle={toggleModal}>
        <ModalHeader>
          {t("candidates.bookmarks.files.addNewFilesHeaderText")}
        </ModalHeader>

        {/*ERRORS*/}
        {errors && Object.keys(errors).length !== 0 ? (
          <FormValidationErrors errors={errors} />
        ) : null}

        <Formik
          initialValues={{ candidatecv_id: candidateCvId, files: [] }}
          onSubmit={(values) => handleUpload(values)}
        >
          <Form>
            <ModalBody>
              <Row>
                <Col sm={12} md={12}>
                  <Input
                    type={"file"}
                    id={"files"}
                    name={"files"}
                    multiple={true}
                    required={true}
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
                    <span style={{ color: "red" }}>
                      {t("errors.invalidFileType")}
                    </span>
                  )}
                  <Alert color={"info"} className={"mt-2"}>
                    <strong>{t("alerts.allowedFormats")}:</strong> docx, .doc,
                    .xdoc, .rtf, .odf, .pdf, .xls, .txt, .xlsm,. xlsx, .jpeg,
                    .jpg, .png, .txt, .cvs, .zip
                  </Alert>
                </Col>
              </Row>
            </ModalBody>
            <ModalFooter>
              <Button
                type={"submit"}
                color={"primary"}
                disabled={candidateLoadingSelector}
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
                {candidateLoadingSelector ? (
                  <Loader />
                ) : (
                  <>{t("buttons.upload")}</>
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
          </Form>
        </Formik>
      </Modal>
    );
  };

  const fileSize = (fileSize) => {
    if (fileSize === 0) return "n/a";
    const i = parseInt(Math.floor(Math.log(fileSize) / Math.log(1024)), 10);
    if (i === 0) return `${fileSize} ${sizes[i]})`;
    return `${(fileSize / 1024 ** i).toFixed(1)} ${sizes[i]}`;
  };

  return (
    <>
      {renderAddFilesModal()}
      <Row className="clearfix">
        <Col lg={12}>
          <Card>
            <CardHeader>
              <h3
                className="card-title"
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
                {t("candidates.bookmarks.files.recentAddedFiles")}
              </h3>
              <div className="card-options">
                <a onClick={() => setModal(true)} style={{ cursor: "pointer" }}>
                  <i
                    className="fa fa-plus"
                    data-toggle="tooltip"
                    data-placement="right"
                    title="Upload New"
                    style={{
                      fontSize: `${
                        settingsSelector.FontSize === "Large"
                          ? "large"
                          : settingsSelector.FontSize === "Extra Large"
                          ? "x-large"
                          : "inherit"
                      }`,
                    }}
                  />
                </a>
              </div>
            </CardHeader>
            <CardBody>
              <div className="file_folder">
                {recentFiles
                  ? recentFiles.map((item, index) => (
                      <a href={item.path} target={"_blank"} key={index}>
                        <div className="icon">
                          {/*<i className="fa fa-folder text-success" />*/}
                        </div>
                        <div className="file-name">
                          <p className="mb-0 text-muted">{item.file_name}</p>
                          <small>{fileSize(item.size)}</small>
                        </div>
                      </a>
                    ))
                  : "No Recent File"}
              </div>
            </CardBody>
          </Card>
          <Card className="bg-none b-none">
            <CardBody className="pt-0">
              <div className="table-responsive">
                {previewFiles ? (
                  <table className="table table-hover table-vcenter table_custom text-nowrap spacing5 text-nowrap mb-0">
                    <thead>
                      <tr>
                        <th />
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
                          {t("candidates.bookmarks.files.name")}
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
                          {t("owner")}
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
                          {t("candidates.bookmarks.files.lastUpdate")}
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
                          {t("candidates.bookmarks.files.fileSize")}
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
                          {t("delete")}
                        </th>
                      </tr>
                    </thead>
                    <tbody>
                      {previewFiles.map((item, index) => (
                        <tr key={index}>
                          <td className="width45">
                            {/*<i className="fa fa-folder" />*/}
                          </td>
                          <td
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
                            <span className="folder-name">
                              <a href={item.path} target={"_blank"}>
                                {item.file_name}
                              </a>
                            </span>
                          </td>
                          <td
                            className="width100"
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
                            <span>
                              {item.userPr === null
                                ? "-"
                                : item.userPr.user.name}
                            </span>
                          </td>
                          <td
                            className="width100"
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
                            <span>{item.updated_at.substring(0, 10)}</span>
                          </td>
                          <td
                            className="width100 text-center"
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
                            <span className="size">{fileSize(item.size)}</span>
                          </td>
                          <td className="width100 text-center">
                            <span className="size">
                              <button
                                type="button"
                                className="btn btn-icon btn-sm js-sweetalert"
                                title="Delete"
                                data-type="confirm"
                                onClick={(event) => {
                                  handleDeleteFile(event, item.id);
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
                                <i className="fa fa-trash-o text-danger" />
                              </button>
                            </span>
                          </td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                ) : (
                  <Row>
                    <Col sm={12} md={12} lg={12}>
                      <Card>
                        <CardBody
                          className={"w-100 d-flex justify-content-center"}
                        >
                          {t("recordNotFound.fileNotFound")}
                        </CardBody>
                      </Card>
                    </Col>
                  </Row>
                )}
              </div>
            </CardBody>
          </Card>
        </Col>
      </Row>
      <Toast />
    </>
  );
};

export default Files;
