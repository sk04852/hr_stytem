import React, { useEffect, useState } from "react";
import {
  Button,
  Card,
  CardBody,
  CardHeader,
  Col,
  Container,
  Input,
  Row,
} from "reactstrap";
import { useFormik } from "formik";
import { useDispatch, useSelector } from "react-redux";
import { useHistory } from "react-router-dom";
import { importFromFile } from "../../../redux/actions/importFromFileActions";
import ManuallyAddedCandidate from "./ManuallyAddedCandidate";
import { Loader } from "../../constants/loaders";
import { useTranslation } from "react-i18next";
import { importFromFileNameURL } from "../../Shared/baseURL";

const ImportFromPDF = () => {
  const dispatch = useDispatch();
  const history = useHistory();

  const { t } = useTranslation();

  const [fileLength, setFileLength] = useState(0); // file length
  const [file, setFile] = useState(""); // file object
  const [cvData, setCvData] = useState([]); // all cvData
  const [formValues, setFormValues] = useState({ b64_string: "" });
  const [viewPdf, setViewPdf] = useState(null); // base64 encoded string

  // console.log(fileLength, file, cvData, viewPdf);

  const settingsSelector = useSelector((state) => state.settings);

  const fileToBase64 = (file, cb) => {
    const reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = function (e) {
      const base64String = reader.result
        .replace("data:", "")
        .replace(/^.+,/, "");
      cb(null, base64String);
      setViewPdf(e.target.result);
    };
    reader.onerror = function (error) {
      cb(error, null);
    };
  };

  const onUploadFileChange = ({ target }) => {
    if (target.files < 1 || !target.validity.valid) {
      return;
    }
    fileToBase64(target.files[0], (err, result) => {
      if (result) {
        setFormValues({ ...formValues, b64_string: result });
        // setFile(target.files[0]);
      }
    });
  };

  const formik = useFormik({
    initialValues: formValues,
    onSubmit: (values) => {
      dispatch(importFromFile(formValues, setFileLength));
    },
  });

  const importDataFromFileSelector = useSelector(
    (state) => state.importFromFile
  );

  useEffect(() => {
    if (importDataFromFileSelector.data) {
      setCvData(importDataFromFileSelector.data);
      if (importDataFromFileSelector.data.filename) {
        const replaceFileNameWithURL =
          importDataFromFileSelector.data.filename.replace(
            "./",
            `${importFromFileNameURL}`
          );
        setFile(replaceFileNameWithURL);
      }
    }
    localStorage.removeItem("candidate-personal-data-form");
  }, [importDataFromFileSelector.data]);

  return (
    <>
      <Container fluid={true}>
        {fileLength === 0 && (
          <div className="w-100 text-right mb-2">
            <Button
              type={"button"}
              color={"primary"}
              className={"mt-2"}
              onClick={() => history.push("/hr-candidate")}
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
              {t("candidates.addNewCandidate.back")}
            </Button>
          </div>
        )}

        <Card>
          <CardHeader className="float-right w-100 justify-content-between">
            <div className={"d-flex flex-row"}>
              <h1>{t("candidates.importFromPdfHeading")}</h1>
            </div>
          </CardHeader>

          <form onSubmit={formik.handleSubmit}>
            <CardBody>
              <Container fluid={true}>
                <Row>
                  <Col sm={12} md={6}>
                    <Input
                      className="form-control"
                      type="file"
                      name="b64_string"
                      onChange={onUploadFileChange}
                      accept="application/pdf"
                      required
                    />
                  </Col>
                </Row>
                <Row className={"mt-3 mb-3 text-md-right"}>
                  <Col sm={12} md={6} className={""}>
                    <Button type="submit" className="btn btn-primary">
                      {importDataFromFileSelector.isLoading ? (
                        <Loader />
                      ) : (
                        <>{t("buttons.upload")}</>
                      )}
                    </Button>
                  </Col>
                </Row>
              </Container>
            </CardBody>
          </form>
        </Card>
        {fileLength > 0 ? (
          <ManuallyAddedCandidate
            cvData={cvData}
            viewPdfFile={viewPdf}
            cvFile={file}
          />
        ) : null}
      </Container>
    </>
  );
};

export default ImportFromPDF;
