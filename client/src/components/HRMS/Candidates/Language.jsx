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
import "./candidate.css";
import { useDispatch, useSelector } from "react-redux";
import { ErrorMessage, Field, FieldArray, Form, Formik } from "formik";
import {
  addCandidateLanguage,
  deleteCandidateLanguage,
  getLanguageById,
  getLanguages,
  updateCandidateLanguage,
} from "../../../redux/actions/languageAction";
import asterik from "../../../assets/images/asterisk.png";
import Toast from "../../constants/toast";
import { Loader } from "../../constants/loaders";
import { useParams } from "react-router-dom";
import {
  FormValidationErrors,
  SubmitFormValidationErrors,
} from "../../constants/errors";
import { useTranslation } from "react-i18next";
import { LanguagesValidationSchema } from "./validations/formValidation";
import { validateHtmlTags } from "./validations/validateHtmlTags";

const Language = ({ candidateCvId }) => {
  const dispatch = useDispatch();
  const { id } = useParams();

  const { t } = useTranslation();

  const initialValues = {
    language: [],
  };

  const newInitialValues = {
    language: [],
  };

  const [formValues, setFormValues] = useState(initialValues);
  const [newFormValues, setNewFormValues] = useState(newInitialValues);
  const [allLanguages, setAllLanguages] = useState([]);
  const [languagesById, setLanguagesById] = useState([]);

  // ERRORS STATE
  const [errors, setErrors] = useState({});
  const [formErrors, setFormErrors] = useState({});

  const fetchCandidateLanguageByID = () => {
    try {
      dispatch(getLanguageById(id));
    } catch (error) {
      throw error;
    }
  };

  const fetchLanguages = async () => {
    try {
      const response = await dispatch(getLanguages());
      setAllLanguages(response.payload.Languages.data);
    } catch (error) {
      throw error;
    }
  };
  useEffect(() => {
    fetchLanguages();
    fetchCandidateLanguageByID();
  }, []);

  // const languageSelector = useSelector((state) => state.languages);
  const languageLoadingSelector = useSelector(
    (state) => state.languages.isLoading
  );
  const getLanguageByIdSelector = useSelector(
    (state) => state.languages.language_by_id.CandidateCVLanguages
  );
  const settingsSelector = useSelector((state) => state.settings);

  useEffect(() => {
    if (getLanguageByIdSelector) {
      setLanguagesById(getLanguageByIdSelector);

      let candidateLanguageData = {
        candidatecv_id: candidateCvId,
        language: [],
      };

      if (languagesById.length > 0) {
        for (let language of languagesById) {
          if (languagesById) {
            candidateLanguageData.language.push({
              id: language.id,
              speaking: language.pivot.speaking,
              reading_writing: language.pivot.reading_writing,
              // level: language.pivot.level,
            });
          } else {
            candidateLanguageData.language = [];
            candidateLanguageData.language.push({
              id: language.id,
              speaking: language.pivot.speaking,
              reading_writing: language.pivot.reading_writing,
              // level: language.pivot.level,
            });
          }
        }
      }
      setFormValues(candidateLanguageData);
    }
  }, [getLanguageByIdSelector, languagesById]);

  const handleUpdateNewLanguage = (values) => {
    // validate
    let error = {};

    values.language.map((item) => {
      error = validateHtmlTags(item, setFormErrors);
    });

    if (Object.keys(error).length === 0) {
      setFormErrors({});
      let formData = new FormData();
      formData.append("candidatecv_id", candidateCvId);
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
      dispatch(updateCandidateLanguage(formData, setErrors));
    }
  };

  const handleSubmit = (values) => {
    // validate
    let error = {};

    values.language.map((item) => {
      error = validateHtmlTags(item, setFormErrors);
    });

    if (Object.keys(error).length === 0) {
      setFormErrors({});
      const formData = new FormData();

      formData.append("candidatecv_id", id);
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
      dispatch(addCandidateLanguage(formData, setErrors));
    }
  };

  const handleLanguageDelete = (languageId) => {
    let languagesIDs = [];
    languagesIDs.push(languageId);

    dispatch(deleteCandidateLanguage(id, languagesIDs, setErrors));
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

        <CardBody>
          <Formik
            initialValues={formValues}
            enableReinitialize={true}
            validationSchema={LanguagesValidationSchema}
            onSubmit={(values) => handleUpdateNewLanguage(values)}
            children={({ values }) => (
              <Form className={"w-100"}>
                <fieldset>
                  <FormGroup>
                    <FieldArray
                      name={"language"}
                      render={(arrayHelpers) => (
                        <Row>
                          {values.language.map((items, index) => (
                            <div className={"w-100"} key={index}>
                              <Row>
                                <Col md={4} sm={4}>
                                  <Label
                                    style={{
                                      fontSize: `${
                                        settingsSelector.FontSize === "Large"
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
                                    as={"select"}
                                    id={`language-name`}
                                    name={`language.${index}.id`}
                                    className={"custom-select"}
                                    required
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
                                    {allLanguages.map((item, index) => (
                                      <option value={item.id} key={index}>
                                        {item.name}
                                      </option>
                                    ))}
                                  </Field>
                                </Col>
                                <Col md={4} sm={4}>
                                  <Label
                                    style={{
                                      fontSize: `${
                                        settingsSelector.FontSize === "Large"
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
                                    <option value={""}>None Selected</option>
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
                                <Col md={4} sm={4}>
                                  <Label
                                    style={{
                                      fontSize: `${
                                        settingsSelector.FontSize === "Large"
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
                                    <option value={""}>None Selected</option>
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
                              {values.language.length > 0 && (
                                <Button
                                  type="button"
                                  color={"danger"}
                                  className={"mb-2 mt-2 ml-1"}
                                  onClick={() => {
                                    const confirm = window.confirm(
                                      "Are You Sure, You Want To Delete This Language?"
                                    );
                                    if (confirm === true) {
                                      handleLanguageDelete(items.id);
                                      arrayHelpers.remove(index);
                                    }
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
                                  {t(
                                    "candidates.bookmarks.languages.deleteLanguage"
                                  )}
                                </Button>
                              )}
                              <Button
                                type={"submit"}
                                color="primary"
                                className={"mb-2 mt-2 ml-1"}
                                disabled={languageLoadingSelector}
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
                                {languageLoadingSelector ? (
                                  <Loader />
                                ) : (
                                  <>{t("buttons.update")}</>
                                )}
                              </Button>
                            </div>
                          ))}
                        </Row>
                      )}
                    />
                  </FormGroup>
                </fieldset>
              </Form>
            )}
          />
          <Formik
            initialValues={newFormValues}
            enableReinitialize={true}
            validationSchema={LanguagesValidationSchema}
            onSubmit={(values) => handleSubmit(values)}
            children={({ values }) => (
              <Form className={"w-100"}>
                <fieldset>
                  <FormGroup>
                    <FieldArray
                      name={"language"}
                      render={(arrayHelpers) => (
                        <>
                          <Row>
                            {values.language && values.language.length > 0
                              ? values.language.map((items, index) => (
                                  <div className={"w-100"} key={index}>
                                    <Row>
                                      <Col md={4} sm={4}>
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
                                          id={`language-name`}
                                          name={`language.${index}.name`}
                                          className={"form-control"}
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
                                          }}
                                        />
                                      </Col>
                                      <Col md={4} sm={4}>
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
                                      <Col md={4} sm={4}>
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
                                    {values.language.length > 0 && (
                                      <Button
                                        type="button"
                                        color={"danger"}
                                        className={"mb-2 mt-2 ml-1"}
                                        onClick={() =>
                                          arrayHelpers.remove(index)
                                        }
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
                                        }}
                                      >
                                        {t(
                                          "candidates.bookmarks.languages.deleteLanguage"
                                        )}
                                      </Button>
                                    )}
                                    <Button
                                      type={"submit"}
                                      color="primary"
                                      className={"mb-2 mt-2 ml-1"}
                                      disabled={languageLoadingSelector}
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
                                      {languageLoadingSelector ? (
                                        <Loader />
                                      ) : (
                                        <>{t("buttons.update")}</>
                                      )}
                                    </Button>
                                  </div>
                                ))
                              : ""}
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
