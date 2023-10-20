import React, { useEffect, useState } from "react";
import {
  Button,
  Card,
  CardBody,
  CardHeader,
  Col,
  FormGroup,
  Label,
  Row,
} from "reactstrap";
import { useDispatch, useSelector } from "react-redux";
import { addCandidateLanguage } from "../../../../redux/actions/languageAction";
import { ErrorMessage, Field, FieldArray, Form, Formik } from "formik";
import "../candidate.css";
import Toast from "../../../constants/toast";
import asterik from "../../../../assets/images/asterisk.png";
import { Loader } from "../../../constants/loaders";
import {
  FormValidationErrors,
  SubmitFormValidationErrors,
} from "../../../constants/errors";
import { AiOutlineLeft, AiOutlineRight } from "react-icons/ai";
import { useTranslation } from "react-i18next";
import { highlightFieldColor } from "../../../Shared/Colors";
import PdfViewer from "./PdfViewer";
import { LanguagesValidationSchema } from "../validations/formValidation";
import { validateHtmlTags } from "../validations/validateHtmlTags";
import handleCandidateCvDataLanguages from "./functions/candidateCvDataFunctions/languagesCvData";
import {
  handleClickedSearchKeywords,
  handleClickedBlurSearchKeywords,
} from "./functions/handleClickedSearchKeywords";

const Language = ({
  candidateCvID,
  cvData,
  viewPdfFile,
  setTabsId,
  cvFile,
}) => {
  const dispatch = useDispatch();

  const { t } = useTranslation();

  const initialValues = {
    language: [
      {
        name: [],
        speaking: [],
        reading_writing: [],
      },
    ],
  };

  const [getAllLanguages, setGetAllLanguages] = useState([]);
  const [formValues, setFormValues] = useState(initialValues);

  // ERRORS STATE
  const [errors, setErrors] = useState({});
  const [formErrors, setFormErrors] = useState({});

  const [candidateImportFromFileUI, setCandidateImportFromFileUI] =
    useState(false);
  const [searchKeywords, setSearchKeywords] = useState([]);
  const [clickedSearchKeywords, setClickedSearchKeywords] = useState([]);

  // const defaultLayoutPluginInstance = defaultLayoutPlugin();

  useEffect(() => {
    const fetAllLanguages = async () => {
      try {
        if (cvData) {
          handleCandidateCvDataLanguages(
            cvData,
            setFormValues,
            setSearchKeywords,
            candidateCvID
          );
        } else {
          setFormValues(initialValues);
        }
      } catch (error) {
        throw error;
      }
    };

    fetAllLanguages();
  }, []);

  const getLanguageSelector = useSelector(
    (state) => state.languages.language.data
  );
  const languageLoadingSelector = useSelector(
    (state) => state.languages.isLoading
  );
  const settingsSelector = useSelector((state) => state.settings);

  useEffect(() => {
    if (getLanguageSelector) {
      setGetAllLanguages(getLanguageSelector.Languages.data);
    }
  }, [getLanguageSelector]);

  const handleSubmit = (values) => {
    // validate
    let error = {};

    values.language.map((item) => {
      error = validateHtmlTags(item, setFormErrors);
    });

    if (Object.keys(error).length === 0) {
      setFormErrors({});
      const formData = new FormData();

      formData.append("candidatecv_id", candidateCvID);
      if (values.language.length > 0) {
        for (let i = 0; i < values.language.length; i++) {
          Object.keys(values.language[i]).map((items) => {
            formData.append(
              `language[${i}][${items}]`,
              values.language[i][items]
            );
          });
        }
      }
      dispatch(addCandidateLanguage(formData, setErrors, setTabsId));
    }
  };

  return (
    <>
      <Card>
        <CardHeader>
          <h1>{t("candidates.bookmarks.languagesBookmark")}</h1>
        </CardHeader>

        {/*DYNAMIC ERRORS*/}
        {errors && Object.keys(errors).length !== 0 ? (
          <FormValidationErrors errors={errors} />
        ) : null}

        {/*FORM INPUT ERRORS*/}
        {formErrors && Object.keys(formErrors).length !== 0 ? (
          <SubmitFormValidationErrors formErrors={formErrors} />
        ) : null}

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
          <Formik
            initialValues={formValues}
            enableReinitialize={true}
            validationSchema={LanguagesValidationSchema}
            onSubmit={(values) => handleSubmit(values)}
            children={({ values }) => (
              <Form className={"w-100"} id={"add-language-manually-form"}>
                <fieldset disabled={languageLoadingSelector}>
                  <FormGroup>
                    <FieldArray
                      name={"language"}
                      render={(arrayHelpers) => (
                        <>
                          <Row>
                            <Col md={candidateImportFromFileUI ? 6 : 12}>
                              {values.language.map((items, index) => (
                                <>
                                  <Row key={index}>
                                    <Col
                                      md={candidateImportFromFileUI ? 12 : 4}
                                      sm={4}
                                    >
                                      <Label
                                        style={{
                                          fontSize: `${
                                            settingsSelector.FontSize ===
                                            "Large"
                                              ? "large"
                                              : settingsSelector.FontSize ===
                                                "Extra Large"
                                              ? "x-large"
                                              : "0.875rem"
                                          }`,
                                        }}
                                      >
                                        {t(
                                          "candidates.bookmarks.languages.language"
                                        )}{" "}
                                        <img
                                          src={asterik}
                                          height={10}
                                          width={10}
                                          className="mt-n2"
                                        />
                                      </Label>
                                      <Field
                                        type={"text"}
                                        id={`language-name.${index}`}
                                        name={`language.${index}.name`}
                                        className={"form-control"}
                                        onClick={(e) =>
                                          handleClickedSearchKeywords(
                                            e,
                                            cvData,
                                            setClickedSearchKeywords
                                          )
                                        }
                                        onBlur={(e) =>
                                          handleClickedBlurSearchKeywords(
                                            e,
                                            cvData,
                                            setClickedSearchKeywords
                                          )
                                        }
                                        required
                                        style={{
                                          fontSize: `${
                                            settingsSelector.FontSize ===
                                            "Large"
                                              ? "large"
                                              : settingsSelector.FontSize ===
                                                "Extra Large"
                                              ? "x-large"
                                              : "14px"
                                          }`,
                                          background: `${
                                            formValues.language &&
                                            formValues.language.length > 0 &&
                                            formValues.language[index]?.name &&
                                            formValues.language[index]?.name[
                                              index
                                            ]
                                              ? highlightFieldColor
                                              : ""
                                          }`,
                                        }}
                                      />
                                      <ErrorMessage
                                        name={`language.${index}.name`}
                                      >
                                        {(msg) => (
                                          <span style={{ color: "red" }}>
                                            {msg}
                                          </span>
                                        )}
                                      </ErrorMessage>
                                    </Col>
                                    <Col
                                      md={candidateImportFromFileUI ? 12 : 4}
                                      sm={4}
                                    >
                                      <Label
                                        style={{
                                          fontSize: `${
                                            settingsSelector.FontSize ===
                                            "Large"
                                              ? "large"
                                              : settingsSelector.FontSize ===
                                                "Extra Large"
                                              ? "x-large"
                                              : "0.875rem"
                                          }`,
                                        }}
                                      >
                                        {t(
                                          "candidates.bookmarks.languages.speaking"
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
                                        className={"custom-select"}
                                        name={`language.${index}.speaking`}
                                        required
                                      >
                                        <option value={""}>
                                          None Selected
                                        </option>
                                        <option value={"A1-A2 kehv"}>
                                          A1-A2 kehv
                                        </option>
                                        <option value={"B1-B2 keskmine"}>
                                          B1-B2 keskmine
                                        </option>
                                        <option value={"C1-C2 hea"}>
                                          C1-C2 hea
                                        </option>
                                      </Field>
                                      <ErrorMessage
                                        name={`language.${index}.speaking`}
                                      >
                                        {(msg) => (
                                          <span style={{ color: "red" }}>
                                            {msg}
                                          </span>
                                        )}
                                      </ErrorMessage>
                                    </Col>
                                    <Col
                                      md={candidateImportFromFileUI ? 12 : 4}
                                      sm={4}
                                    >
                                      <Label
                                        style={{
                                          fontSize: `${
                                            settingsSelector.FontSize ===
                                            "Large"
                                              ? "large"
                                              : settingsSelector.FontSize ===
                                                "Extra Large"
                                              ? "x-large"
                                              : "0.875rem"
                                          }`,
                                        }}
                                      >
                                        {t(
                                          "candidates.bookmarks.languages.reading"
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
                                        className={"custom-select"}
                                        name={`language.${index}.reading_writing`}
                                        required
                                      >
                                        <option value={""}>
                                          None Selected
                                        </option>
                                        <option value={"A1-A2 kehv"}>
                                          A1-A2 kehv
                                        </option>
                                        <option value={"B1-B2 keskmine"}>
                                          B1-B2 keskmine
                                        </option>
                                        <option value={"C1-C2 hea"}>
                                          C1-C2 hea
                                        </option>
                                      </Field>
                                      <ErrorMessage
                                        name={`language.${index}.reading_writing`}
                                      >
                                        {(msg) => (
                                          <span style={{ color: "red" }}>
                                            {msg}
                                          </span>
                                        )}
                                      </ErrorMessage>
                                    </Col>
                                  </Row>
                                  {values.language.length > 1 && (
                                    <Button
                                      type="button"
                                      color={"danger"}
                                      className={`mb-2 mt-2 ml-1`}
                                      onClick={() => arrayHelpers.remove(index)}
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
                                      {t(
                                        "candidates.bookmarks.languages.deleteLanguage"
                                      )}
                                    </Button>
                                  )}
                                </>
                              ))}
                            </Col>
                            <Col md={6}>
                              {candidateImportFromFileUI ? (
                                <>
                                  {viewPdfFile ? (
                                    <PdfViewer
                                      cvFile={cvFile}
                                      cvData={cvData}
                                      searchKeywords={searchKeywords}
                                      clickedSearchKeywords={
                                        clickedSearchKeywords
                                      }
                                    />
                                  ) : null}
                                </>
                              ) : null}
                            </Col>
                          </Row>
                          <Button
                            type="button"
                            color={"primary"}
                            className={`mb-2 mt-2`}
                            onClick={() => {
                              arrayHelpers.push({
                                name: "",
                                // level: "",
                                speaking: "",
                                reading_writing: "",
                              });
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
                            {t(
                              "candidates.bookmarks.languages.addMoreLanguages"
                            )}
                          </Button>
                        </>
                      )}
                    />
                  </FormGroup>
                  <div className={"w-100 d-flex flex-row justify-content-end"}>
                    <Button
                      type={"submit"}
                      color="primary"
                      className={"mr-3"}
                      disabled={languageLoadingSelector}
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
                      {languageLoadingSelector ? (
                        <Loader />
                      ) : (
                        <>{t("buttons.save")}</>
                      )}
                    </Button>
                  </div>
                </fieldset>
              </Form>
            )}
          />
        </CardBody>
      </Card>
      <Toast />
    </>
  );
};

export default Language;
