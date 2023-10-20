import React, { useCallback, useEffect, useState } from "react";
import {
  Button,
  Card,
  CardBody,
  CardHeader,
  CardTitle,
  Col,
  Input,
  Label,
  Row,
  Alert,
} from "reactstrap";
import { useDispatch, useSelector } from "react-redux";
import { useLocation } from "react-router-dom";
import {
  createCompanies,
  fetchCompanyData,
  getAllCompaniesIndustry,
} from "../../../redux/actions/companiesActions";
import asterik from "../../../assets/images/asterisk.png";
import { Field, FieldArray, Form, Formik, ErrorMessage } from "formik";
import Toast from "../../constants/toast";
import { Loader } from "../../constants/loaders";
import { useHistory } from "react-router-dom";
import {
  FormValidationErrors,
  SubmitFormValidationErrors,
} from "../../constants/errors";
import { AutoComplete } from "antd";
import axios from "axios";
import { baseURL } from "../../Shared/baseURL";
import { useTranslation } from "react-i18next";
import { CompanyValidationSchema } from "./validations/formValidations";
import { validateHtmlTags } from "./validations/validateHtmlTags";
import { SUPPORTED_COMPANY_FILES_FORMATS } from "../../Shared/SupportedFilesExtenstions";

const AddNewCompany = () => {
  const dispatch = useDispatch();
  const history = useHistory();
  const location = useLocation();

  const { t } = useTranslation();

  const initialValues = {
    invoicing_info: "",
    industry_names: [""],
    // address: "",
    vat: "",
    en: {
      company_name: "",
      contacts: [
        {
          name: "",
          email: "",
          phone: "",
          position: "",
        },
      ],
      location: [""],
    },
    files: [],
  };

  const [formValues, setFormValues] = useState(initialValues);
  const [uploadFiles, setUploadFiles] = useState([]);
  const [check, setcheck] = useState(false);
  const [companyNameOptions, setCompanyNameOptions] = useState([]);
  const [companyName, setCompanyName] = useState("");
  const [companyNameRequired, setCompanyNameRequired] = useState(false);

  // ERRORS STATE
  const [errors, setErrors] = useState({});
  const [formErrors, setFormErrors] = useState({});
  const [fileTypeError, setFileTypeError] = useState(false);

  useEffect(() => {
    const fetchAllCompaniesIndustry = async () => {
      try {
        const response = await dispatch(getAllCompaniesIndustry());
      } catch (error) {
        throw error;
      }
    };
    fetchAllCompaniesIndustry();
  }, []);

  const companiesSelector = useSelector((state) => state.companies);
  const settingsSelector = useSelector((state) => state.settings);

  const handleFileChange = (e) => {
    let files = e.target.files;
    for (const key in files) {
      if (Object.hasOwnProperty.call(files, key)) {
        const element = files[key];
        if (SUPPORTED_COMPANY_FILES_FORMATS.includes(element.type)) {
          setFileTypeError(false);
          setUploadFiles([...uploadFiles, files]);
        } else {
          setFileTypeError(true);
        }
      }
    }
  };

  const handleSubmit = (values) => {
    // validate
    let error = {};
    const location = "AddCompany";
    error = validateHtmlTags(values, setFormErrors, location);

    // company name check
    if (values.en.company_name === "") {
      setCompanyNameRequired(true);
    } else setCompanyNameRequired(false);

    // submit form if there is no error
    if (
      Object.keys(error).length === 0 &&
      companyNameRequired === false &&
      !fileTypeError
    ) {
      let files = [];
      if (uploadFiles.length > 0) {
        for (let i = 0; i <= uploadFiles.length; i++) {
          files.push(uploadFiles[0][i]);
        }
      }

      const formData = new FormData();
      if (files.length > 0) {
        for (const file of files) {
          formData.append("files[]", file);
        }
      }

      formData.append("invoicing_info", values.invoicing_info);

      if (values.industry_names.length > 0) {
        for (let i = 0; i < values.industry_names.length; i++) {
          formData.append("industry_names[]", values.industry_names[i]);
        }
      }
      formData.append("vat", values.vat);
      formData.append(
        "en[company_name]",
        values.en.company_name ? values.en.company_name : companyName
      );
      if (values.en.contacts.length > 0) {
        for (let i = 0; i < values.en.contacts.length; i++) {
          Object.keys(values.en.contacts[i]).map((key) => {
            formData.append(
              `en[contacts][${i}][${key}]`,
              values.en.contacts[i][key]
            );
          });
        }
      }
      if (values.en.location.length > 0) {
        for (let i = 0; i < values.en.location.length; i++) {
          formData.append(`en[location][${i}]`, values.en.location[i]);
        }
      }
      setcheck(true);
      dispatch(createCompanies(formData, setUploadFiles, setErrors, history));
    }
  };

  const handleFetchCompany = () => {
    let company_name = document.getElementById("company-name");
    dispatch(fetchCompanyData(company_name.value, setFormValues));
  };

  const debounce = (func) => {
    let timer;
    return function (...args) {
      const context = this;
      if (timer) clearTimeout(timer);
      timer = setTimeout(() => {
        timer = null;
        func.apply(context, args);
      }, 500);
    };
  };

  const onSelect = (data, option) => {
    dispatch(fetchCompanyData(data, setFormValues));
  };

  const onChange = (data) => {
    if (data.length > 0) {
      setCompanyNameRequired(false);
      setCompanyName(data);
    } else {
      setCompanyNameRequired(true);
    }
  };

  const handleCompanyNameChange = (e) => {
    let searchValue = e;
    let searchOptions = [];
    if (searchValue.length > 1) {
      const token = localStorage.getItem("token");
      axios
        .post(
          `${baseURL}api/companies/fetch-company-data`,
          { company_name: searchValue },
          {
            headers: {
              Authorization: `Bearer ${token}`,
            },
          }
        )
        .then((response) => {
          if (response.data.data.keha.ettevotjad.item.length > 0) {
            response.data.data.keha.ettevotjad.item.forEach((item) => {
              searchOptions.push({ value: item.nimi, label: item.nimi });
            });
            setCompanyNameOptions(searchOptions);
          }
        })
        .catch((error) => {
          return error;
        });
    }
  };

  const optimizedCompanyNameChangeFn = useCallback(
    debounce(handleCompanyNameChange),
    []
  );

  return (
    <>
      <Col lg={12} md={12}>
        <Card>
          <CardHeader>
            <CardTitle>
              <h2>{t("companies.addNewCompany.heading")}</h2>
            </CardTitle>
          </CardHeader>

          {/*ERRORS*/}
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
              validationSchema={CompanyValidationSchema}
              onSubmit={(values) => handleSubmit(values)}
              children={({ values }) => (
                <Form id={"add-companies-form"}>
                  <fieldset disabled={companiesSelector.isLoading}>
                    <h6
                      style={{
                        fontSize: `${
                          settingsSelector.FontSize === "Large"
                            ? "large"
                            : settingsSelector.FontSize === "Extra Large"
                            ? "x-large"
                            : "1rem"
                        }`,
                      }}
                    >
                      <strong>
                        {t("companies.addNewCompany.companyDetails.heading")}
                      </strong>
                    </h6>
                    <Row>
                      <Col md={3} sm={3}>
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
                          {t("companies.addNewCompany.companyDetails.name")}{" "}
                          <img
                            src={asterik}
                            height={10}
                            width={10}
                            className="mt-n2"
                          />
                        </Label>
                        <AutoComplete
                          options={companyNameOptions}
                          style={{ width: "100%" }}
                          onSelect={onSelect}
                          onChange={onChange}
                          onSearch={optimizedCompanyNameChangeFn}
                          name={"en.company_name"}
                          id={"company-name"}
                        />
                        {companyNameRequired && (
                          <span style={{ color: "red" }}>Required</span>
                        )}
                        <div className={"mt-2"}>
                          <Button
                            color={"primary"}
                            type={"button"}
                            onClick={handleFetchCompany}
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
                            {companiesSelector.isLoading ? (
                              <Loader />
                            ) : (
                              <>
                                {t(
                                  "companies.addNewCompany.companyDetails.fetchCompanyButton"
                                )}
                              </>
                            )}
                          </Button>
                        </div>
                      </Col>
                      <Col md={3} sm={3}>
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
                            "companies.addNewCompany.companyDetails.registerCode"
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
                          id={"invoicing_info"}
                          name={"invoicing_info"}
                          className={"form-control"}
                          // disabled={true}
                          required
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
                        <ErrorMessage name={`invoicing_info`}>
                          {(msg) => <span style={{ color: "red" }}>{msg}</span>}
                        </ErrorMessage>
                      </Col>
                      <Col md={3} sm={3}>
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
                          {t("companies.addNewCompany.companyDetails.vat")}
                        </Label>
                        <Field
                          type={"text"}
                          id={"vat"}
                          name={"vat"}
                          className={"form-control"}
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
                        <ErrorMessage name={`vat`}>
                          {(msg) => <span style={{ color: "red" }}>{msg}</span>}
                        </ErrorMessage>
                      </Col>
                    </Row>
                    <div className={"mt-4"}>
                      <h6
                        style={{
                          fontSize: `${
                            settingsSelector.FontSize === "Large"
                              ? "large"
                              : settingsSelector.FontSize === "Extra Large"
                              ? "x-large"
                              : "1rem"
                          }`,
                        }}
                      >
                        <strong>
                          {t("companies.addNewCompany.personContact.heading")}
                        </strong>
                      </h6>
                    </div>
                    <FieldArray
                      name={"en.contacts"}
                      render={(arrayHelpers) => (
                        <Row>
                          {values.en.contacts.map((items, index) => (
                            <Row className={"w-100 ml-1"} key={index}>
                              <Col md={3} sm={3}>
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
                                    "companies.addNewCompany.personContact.name"
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
                                  id={`name`}
                                  name={`en.contacts.${index}.name`}
                                  className={"form-control"}
                                  placeholder={"Nimi"}
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
                                />
                                <ErrorMessage
                                  name={`en.contacts.${index}.name`}
                                >
                                  {(msg) => (
                                    <span style={{ color: "red" }}>{msg}</span>
                                  )}
                                </ErrorMessage>
                              </Col>
                              <Col md={3} sm={3}>
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
                                    "companies.addNewCompany.personContact.email"
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
                                  id={"email"}
                                  name={`en.contacts.${index}.email`}
                                  className={"form-control"}
                                  placeholder={"Email"}
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
                                />
                                <ErrorMessage
                                  name={`en.contacts.${index}.email`}
                                >
                                  {(msg) => (
                                    <span style={{ color: "red" }}>{msg}</span>
                                  )}
                                </ErrorMessage>
                              </Col>
                              <Col md={3} sm={3}>
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
                                    "companies.addNewCompany.personContact.phone"
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
                                  id={"phone"}
                                  name={`en.contacts.${index}.phone`}
                                  className={"form-control"}
                                  placeholder={"Telefon"}
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
                                />
                                <ErrorMessage
                                  name={`en.contacts.${index}.phone`}
                                >
                                  {(msg) => (
                                    <span style={{ color: "red" }}>{msg}</span>
                                  )}
                                </ErrorMessage>
                              </Col>
                              <Col md={3} sm={3}>
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
                                    "companies.addNewCompany.personContact.position"
                                  )}
                                </Label>
                                <Field
                                  type={"text"}
                                  id={"position"}
                                  name={`en.contacts.${index}.position`}
                                  className={"form-control"}
                                  placeholder={"Amet"}
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
                                />
                                <ErrorMessage
                                  name={`en.contacts.${index}.position`}
                                >
                                  {(msg) => (
                                    <span style={{ color: "red" }}>{msg}</span>
                                  )}
                                </ErrorMessage>
                              </Col>
                              {values.en.contacts.length > 1 && (
                                <Button
                                  type="button"
                                  color={"danger"}
                                  className={"mb-2 mt-2 ml-1"}
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
                                    "companies.addNewCompany.personContact.deleteContact"
                                  )}
                                </Button>
                              )}
                            </Row>
                          ))}
                          <Button
                            type="button"
                            color={"primary"}
                            className={`mb-2 ml-2 mt-2`}
                            onClick={() => {
                              arrayHelpers.push({
                                name: "",
                                email: "",
                                phone: "",
                                position: "",
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
                              "companies.addNewCompany.personContact.addMoreContacts"
                            )}
                          </Button>
                        </Row>
                      )}
                    />
                    <Row>
                      <Col md={6} sm={6}>
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
                          {t("companies.addNewCompany.industry")}{" "}
                          <img
                            src={asterik}
                            height={10}
                            width={10}
                            className="mt-n2"
                          />
                        </Label>
                        <FieldArray
                          name={"industry_names"}
                          render={(arrayHelpers) => (
                            <div>
                              {values.industry_names.length > 0 ? (
                                values.industry_names.map((c, index) => (
                                  <div key={index} className={"w-100 d-flex"}>
                                    <div className={"w-100 flex-row"}>
                                      <Field
                                        name={`industry_names.${index}`}
                                        className={`form-control ${
                                          index === 0 ? "" : "mt-1"
                                        }`}
                                        placeholder={`Industry`}
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
                                      <ErrorMessage
                                        name={`industry_names.${index}`}
                                      >
                                        {(msg) => (
                                          <span style={{ color: "red" }}>
                                            {msg}
                                          </span>
                                        )}
                                      </ErrorMessage>
                                    </div>
                                    <Button
                                      type="button"
                                      className={`ml-1 ${
                                        index === 0 ? "" : "mt-1"
                                      }`}
                                      color={"primary"}
                                      onClick={() => arrayHelpers.remove(index)} // remove a location from the list
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
                                      -
                                    </Button>
                                    <Button
                                      type="button"
                                      className={`ml-1 ${
                                        index === 0 ? "" : "mt-1"
                                      }`}
                                      color={"primary"}
                                      onClick={() =>
                                        arrayHelpers.insert(index, "")
                                      } // insert an empty string at a position
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
                                      +
                                    </Button>
                                  </div>
                                ))
                              ) : (
                                <Button
                                  type="button"
                                  color={"primary"}
                                  onClick={() => arrayHelpers.push("")}
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
                                  {t("companies.addNewCompany.addIndustry")}
                                </Button>
                              )}
                            </div>
                          )}
                        />
                      </Col>
                      <Col md={6} sm={6}>
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
                          {t("companies.addNewCompany.location")}{" "}
                          <img
                            src={asterik}
                            height={10}
                            width={10}
                            className="mt-n2"
                          />
                        </Label>
                        <FieldArray
                          name={"en.location"}
                          render={(arrayHelpers) => (
                            <div>
                              {values.en.location.length > 0 ? (
                                values.en.location.map((c, index) => (
                                  <div key={index} className={"d-flex"}>
                                    <div className={"w-100 flex-row"}>
                                      <Field
                                        name={`en.location.${index}`}
                                        className={`form-control ${
                                          index === 0 ? "" : "mt-1"
                                        }`}
                                        placeholder={`Aadress`}
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
                                      <ErrorMessage
                                        name={`en.location.${index}`}
                                      >
                                        {(msg) => (
                                          <span style={{ color: "red" }}>
                                            {msg}
                                          </span>
                                        )}
                                      </ErrorMessage>
                                    </div>
                                    <Button
                                      type="button"
                                      className={`ml-1 ${
                                        index === 0 ? "" : "mt-1"
                                      }`}
                                      color={"primary"}
                                      onClick={() => arrayHelpers.remove(index)} // remove a location from the list
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
                                      -
                                    </Button>
                                    <Button
                                      type="button"
                                      className={`ml-1 ${
                                        index === 0 ? "" : "mt-1"
                                      }`}
                                      color={"primary"}
                                      onClick={() =>
                                        arrayHelpers.insert(index, "")
                                      } // insert an empty string at a position
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
                                      +
                                    </Button>
                                  </div>
                                ))
                              ) : (
                                <Button
                                  type="button"
                                  color={"primary"}
                                  onClick={() => arrayHelpers.push("")}
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
                                  {t("companies.addNewCompany.addLocation")}
                                </Button>
                              )}
                            </div>
                          )}
                        />
                      </Col>
                    </Row>
                    <Row className={"mt-4"}>
                      <Col md={4} sm={4}>
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
                          {t("companies.addNewCompany.uploadFiles")}
                        </Label>
                        <Input
                          type={"file"}
                          multiple={true}
                          name={"files"}
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
                          <strong>{t("alerts.allowedFormats")}:</strong> docx,
                          xls, csv, zip, pdf, jpg, png
                        </Alert>
                      </Col>
                    </Row>
                    <Row>
                      <div className={"w-100 text-right"}>
                        <Button
                          type={"submit"}
                          color={"primary"}
                          className={"mr-2"}
                          disabled={companiesSelector.isLoading}
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
                          {companiesSelector.isLoading ? (
                            <Loader />
                          ) : (
                            <>{t("buttons.save")}</>
                          )}
                        </Button>

                        {location.state === undefined ? (
                          <>
                            <Button
                              type={"button"}
                              color={"primary"}
                              onClick={() => history.push("/hr-companies")}
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
                              {t("buttons.back")}
                            </Button>
                          </>
                        ) : location.state && location.state.addJob !== null ? (
                          <>
                            <Button
                              type="button"
                              color={"primary"}
                              className={`mb-2 ml-2 mt-2`}
                              onClick={() =>
                                history.push(location.state.addJob)
                              }
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
                              {t("companies.addNewCompany.backToJobsPage")}
                            </Button>
                          </>
                        ) : location.state &&
                          location.state.editJobs !== null ? (
                          <>
                            <Button
                              type="button"
                              color={"primary"}
                              className={`mb-2 ml-2 mt-2`}
                              onClick={() =>
                                history.push(location.state.editJobs)
                              }
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
                              {t("companies.addNewCompany.backToEditJobsPage")}
                            </Button>
                          </>
                        ) : (
                          <>
                            <Button
                              type={"button"}
                              color={"primary"}
                              onClick={() => history.push("/hr-companies")}
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
                              {t("buttons.back")}
                            </Button>
                          </>
                        )}
                      </div>
                    </Row>
                  </fieldset>
                </Form>
              )}
            />
          </CardBody>
        </Card>
      </Col>
      <Toast />
    </>
  );
};

export default AddNewCompany;
