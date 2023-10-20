import React, { useEffect, useState } from "react";
import {
  Alert,
  Button,
  Card,
  CardBody,
  CardHeader,
  Col,
  Input,
  Row,
} from "reactstrap";
import "../candidate.css";
import { Form, Formik } from "formik";
import { useDispatch, useSelector } from "react-redux";
import {
  deleteUploadFilesById,
  getUploadFilesById,
  uploadFiles,
} from "../../../../redux/actions/candidatesAction";
import { Loader } from "../../../constants/loaders";
import Toast from "../../../constants/toast";
import { FormValidationErrors } from "../../../constants/errors";
import { defaultLayoutPlugin } from "@react-pdf-viewer/default-layout";
// import { Viewer, Worker } from "@react-pdf-viewer/core";
import { AiOutlineLeft, AiOutlineRight } from "react-icons/ai";
import { useTranslation } from "react-i18next";
import PdfViewer from "./PdfViewer";

const Files = ({ candidateCvID, viewPdfFile, setTabsId }) => {
  const dispatch = useDispatch();

  const { t } = useTranslation();

  const [uploadCandidateFiles, setUploadCandidateFiles] = useState([]);
  const [previewFiles, setPreviewFiles] = useState([]);
  const [errors, setErrors] = useState({});
  const [fileTypeError, setFileTypeError] = useState(false);
  const [candidateImportFromFileUI, setCandidateImportFromFileUI] =
    useState(false);
  const [triggerUpdate, setTriggerUpdate] = useState(null);

  const defaultLayoutPluginInstance = defaultLayoutPlugin();

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

  const fetchCandidateFilesByID = async () => {
    try {
      const response = await dispatch(getUploadFilesById(candidateCvID));
      setPreviewFiles(response.payload.data.CandidateCVFiles);
    } catch (error) {
      throw error;
    }
  };

  useEffect(() => {
    fetchCandidateFilesByID();
  }, [triggerUpdate]);

  const candidateLoadingSelector = useSelector(
    (state) => state.candidates.isLoading
  );

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
          candidateCvID,
          [],
          setErrors,
          fetchCandidateFilesByID,
          setTabsId
        )
      );
    }
  };

  const handleDeleteFile = (e, id) => {
    const confirm = window.confirm("Are You Sure?");
    if (confirm === true) {
      dispatch(deleteUploadFilesById(id, candidateCvID, setTriggerUpdate));
    }
  };

  const sizes = ["Bytes", "KB", "MB", "GB", "TB"];
  const fileSize = (fileSize) => {
    if (fileSize === 0) return "n/a";
    const i = parseInt(Math.floor(Math.log(fileSize) / Math.log(1024)), 10);
    if (i === 0) return `${fileSize} ${sizes[i]})`;
    return `${(fileSize / 1024 ** i).toFixed(1)} ${sizes[i]}`;
  };

  const settingsSelector = useSelector((state) => state.settings);

  return (
    <>
      <Card>
        <CardHeader className="float-right">
          <h1>{t("candidates.bookmarks.filesBookmark")}</h1>
        </CardHeader>

        {/*/!*ERRORS*!/*/}
        {errors && Object.keys(errors).length !== 0 ? (
          <FormValidationErrors errors={errors} />
        ) : null}

        {viewPdfFile ? (
          <Row>
            <Col sm={12} md={12} className={"w-100 d-flex justify-content-end"}>
              <Button
                type={"button"}
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
          <Row>
            <Col sm={6}>
              <Formik
                initialValues={{ candidatecv_id: candidateCvID, files: [] }}
                onSubmit={(values) => handleUpload(values)}
                children={({ values, errors, touched }) => (
                  <Form>
                    <Input
                      type={"file"}
                      id={"files"}
                      name={"files"}
                      required
                      multiple={true}
                      onChange={(e) => {
                        handleFileChange(e);
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
                    {fileTypeError && (
                      <span style={{ color: "red" }}>
                        {t("errors.invalidFileType")}
                      </span>
                    )}
                    <Alert color={"info"} className={"mt-2"}>
                      <strong>{t("alerts.allowedFormats")}:</strong> docx, .doc,
                      .xdoc, .rtf, .odf, .pdf, .xls, .txt, .xlsm,. xlsx, .jpeg,
                      .jpg, .png, .cvs, .zip
                    </Alert>
                    <div className={"mt-2 text-right"}>
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
                          <>
                            {t("candidates.bookmarks.files.uploadFilesButton")}
                          </>
                        )}
                      </Button>
                    </div>
                  </Form>
                )}
              />
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
                          {item.userPr === null ? "-" : item.userPr.user.name}
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
                                  : settingsSelector.FontSize === "Extra Large"
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
                    <CardBody className={"w-100 d-flex justify-content-center"}>
                      {t("recordNotFound.fileNotFound")}
                    </CardBody>
                  </Card>
                </Col>
              </Row>
            )}
          </div>
        </CardBody>
      </Card>
      <Toast />
    </>
  );
};

export default Files;
