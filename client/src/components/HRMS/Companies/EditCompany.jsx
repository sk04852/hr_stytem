import React, { useEffect, useState } from "react";
import { useParams, useHistory } from "react-router-dom";
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
import {
  getAllCompaniesIndustry,
  getAllCompanyProfileById,
  updateCompany,
} from "../../../redux/actions/companiesActions";
import { useDispatch, useSelector } from "react-redux";
import { Field, FieldArray, Form, Formik, ErrorMessage } from "formik";
import Toast from "../../constants/toast";
import { Loader } from "../../constants/loaders";
import asterik from "../../../assets/images/asterisk.png";
import {
  FormValidationErrors,
  SubmitFormValidationErrors,
} from "../../constants/errors";
import { baseURL } from "../../Shared/baseURL";
import { useTranslation } from "react-i18next";
import { EditCompanyValidationSchema } from "./validations/formValidations";
import { validateHtmlTags } from "./validations/validateHtmlTags";
import { SUPPORTED_COMPANY_FILES_FORMATS } from "../../Shared/SupportedFilesExtenstions";

const EditCompany = () => {
  const { id } = useParams();
  let history = useHistory();
  let dispatch = useDispatch();

  const { t } = useTranslation();

  const initialValues = {
    company_pr_id: "",
    invoicing_info: "",
    industries: [
      {
        id: "",
        name: "",
      },
    ],
    address: "",
    vat: "",
    en: {
      company_id: "",
      company_name: "",
      contacts: [
        {
          id: "",
          name: "",
          email: "",
          phone: "",
          position: "",
        },
      ],
      location: [
        {
          id: "",
          location: "",
        },
      ],
    },
    files: [],
    old_files: [],
  };

  const [formValues, setFormValues] = useState(initialValues);
  const [uploadFiles, setUploadFiles] = useState([]);
  const [previewFiles, setPreviewFiles] = useState([]);
  const [deleteRowId, setDeleteRowId] = useState([]);
  const [industryOptions, setIndustryOptions] = useState([]);
  const [companyNameRequired, setCompanyNameRequired] = useState(false);

  // ERRORS STATE
  const [errors, setErrors] = useState({});
  const [formErrors, setFormErrors] = useState({});
  const [fileTypeError, setFileTypeError] = useState(false);

  useEffect(() => {
    const fetchCompanyProfile = async () => {
      try {
        const response = await dispatch(getAllCompanyProfileById(id));
        let populateData = {
          company_pr_id: response.payload.company.id,
          invoicing_info: response.payload.company.invoicing_info,
          industries: [],
          vat:
            response.payload.company.vat === null
              ? ""
              : response.payload.company.vat,
          en: {},
          files: [],
        };

        if (response.payload.company.company.length > 0) {
          for (let company of response.payload.company.company) {
            populateData[company.locale].company_id = company.id;
            populateData[company.locale].company_name = company.name;
          }
        }

        if (response.payload.company.companyContacts.length > 0) {
          for (let contact of response.payload.company.companyContacts) {
            if (populateData[contact.locale].hasOwnProperty("contacts")) {
              populateData[contact.locale].contacts.push({
                id: contact.id,
                name: contact.name,
                email: contact.email,
                phone: contact.phone,
                position: contact.position === null ? "" : contact.position,
              });
            } else {
              populateData[contact.locale].contacts = [];
              populateData[contact.locale].contacts.push({
                id: contact.id,
                name: contact.name,
                email: contact.email,
                phone: contact.phone,
                position: contact.position === null ? "" : contact.position,
              });
            }
          }
        }

        if (response.payload.company.industries.length > 0) {
          for (let industry of response.payload.company.industries) {
            populateData.industries.push({
              id: industry.id,
              name: industry.name,
            });
          }
        }

        if (response.payload.company.companylocations.length > 0) {
          for (let contact of response.payload.company.companylocations) {
            if (populateData[contact.locale].hasOwnProperty("location")) {
              populateData[contact.locale].location.push({
                id: contact.id,
                location: contact.location,
              });
            } else {
              populateData[contact.locale].location = [];
              populateData[contact.locale].location.push({
                id: contact.id,
                location: contact.location,
              });
            }
          }
        }

        if (response.payload.company.companyFiles.length > 0) {
          for (let file of response.payload.company.companyFiles) {
            if (populateData.hasOwnProperty("files")) {
              populateData.files.push({
                id: file.id,
                file_name: file.file_name,
              });
            } else {
              populateData.files = [];
              populateData.files.push({
                id: file.id,
                file_name: file.file_name,
              });
            }
            setPreviewFiles(populateData.files);
          }
        }

        if (response.payload.company.companyFiles.length > 0) {
          for (let oldFile of response.payload.company.companyFiles) {
            if (populateData.hasOwnProperty("old_files")) {
              populateData.old_files.push({
                id: oldFile.id,
              });
            } else {
              populateData.old_files = [];
              populateData.old_files.push({
                id: oldFile.id,
              });
            }
            setDeleteRowId(populateData.old_files);
          }
        }
        setFormValues(populateData);
      } catch (e) {}
    };
    fetchCompanyProfile();
  }, []);

  useEffect(() => {
    dispatch(getAllCompaniesIndustry());

    if (getAllCompaniesIndustrySelector) {
      let options = [];
      getAllCompaniesIndustrySelector.forEach((item) => {
        options.push({ value: item.id, label: item.name });
      });
      setIndustryOptions(options);
    }
  }, []);

  const companySelector = useSelector((state) => state.companies);
  const getAllCompaniesIndustrySelector = useSelector(
    (state) => state.companies.company_industry
  );
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

  const handleUpdate = (values) => {
    // validate
    let error = {};
    const location = "EditCompany";
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

      for (let i = 0; i < deleteRowId.length; i++) {
        formData.append(`old_files[${i}][id]`, deleteRowId[i].id);
      }

      formData.append("company_pr_id", id);
      formData.append("invoicing_info", values.invoicing_info);

      if (values.industries.length > 0) {
        for (let i = 0; i < values.industries.length; i++) {
          formData.append(`industries[${i}][id]`, values.industries[i].id);
        }
      }

      formData.append("vat", values.vat);
      formData.append("en[company_id]", values.en.company_id);
      formData.append("en[company_name]", values.en.company_name);
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
          formData.append(`en[location][${i}][id]`, values.en.location[i].id);
          formData.append(
            `en[location][${i}][location]`,
            values.en.location[i].location
          );
        }
      }

      dispatch(updateCompany(formData, id, setErrors, setUploadFiles, history));
    }
  };

  const handleSelectedDivDelete = (id) => {
    document.getElementById(`id-${id}`).remove();
  };

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
              validationSchema={EditCompanyValidationSchema}
              onSubmit={(values) => handleUpdate(values)}
              enableReinitialize
              children={({ values }) => (
                <Form>
                  <fieldset disabled={companySelector.isLoading}>
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
                        <Field type={"hidden"} name={"en.company_id"} />
                        <Field
                          type={"text"}
                          id={"company_name"}
                          name={"en.company_name"}
                          placeholder={"Company Name"}
                          className={"form-control"}
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
                        {companyNameRequired && (
                          <span style={{ color: "red" }}>Required</span>
                        )}
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
                              <Field
                                type={"hidden"}
                                name={`en.contacts.${index}.id`}
                              />
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
                                  id={"name"}
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
                                  id={"email "}
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
                            className={`mb-2 ml-2`}
                            onClick={() => {
                              arrayHelpers.push({
                                id: "",
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
                          name={"industries"}
                          placeholder={"Company Industry"}
                          render={(arrayHelpers) => (
                            <div>
                              {values.industries &&
                              values.industries.length > 0 ? (
                                values.industries.map((c, index) => (
                                  <div key={index} className={"w-100"}>
                                    <div
                                      className={`w-100 d-flex flex-row ${
                                        index > 0 ? "mt-2" : ""
                                      }`}
                                    >
                                      <Field
                                        as={"select"}
                                        id={"company_industry"}
                                        name={`industries.${index}.id`}
                                        className={"custom-select"}
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
                                      >
                                        <option>Ei ole valitud</option>
                                        {getAllCompaniesIndustrySelector.map(
                                          (items, index) => (
                                            <option
                                              key={index}
                                              value={items.id}
                                            >
                                              {items.name}
                                            </option>
                                          )
                                        )}
                                      </Field>
                                      <Button
                                        type="button"
                                        className={`ml-1 ${
                                          index === 0 ? "" : "mt-1"
                                        }`}
                                        color={"primary"}
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
                                        -
                                      </Button>
                                      <Button
                                        type="button"
                                        className={`ml-1 ${
                                          index === 0 ? "" : "mt-1"
                                        }`}
                                        color={"primary"}
                                        onClick={() => {
                                          // setAddNewIndustry(true);
                                          arrayHelpers.insert(index, {
                                            id: "",
                                            name: "",
                                          });
                                        }} // insert an empty string at a position
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
                                        +
                                      </Button>
                                    </div>
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
                          placeholder={"Company Location"}
                          render={(arrayHelpers) => (
                            <div>
                              {values.en.location &&
                              values.en.location.length > 0 ? (
                                values.en.location.map((c, index) => (
                                  <div key={index}>
                                    <div className={"d-flex"}>
                                      <Field
                                        type={"hidden"}
                                        name={`en.location.${index}.id`}
                                      />
                                      <Field
                                        name={`en.location.${index}.location`}
                                        className={`form-control ${
                                          index === 0 ? "" : "mt-1"
                                        }`}
                                        placeholder={`Company Location`}
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
                                      <Button
                                        type="button"
                                        className={`ml-1 ${
                                          index === 0 ? "" : "mt-1"
                                        }`}
                                        color={"primary"}
                                        onClick={() =>
                                          arrayHelpers.remove(index)
                                        } // remove a friend from the list
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
                                        -
                                      </Button>
                                      <Button
                                        type="button"
                                        className={`ml-1 ${
                                          index === 0 ? "" : "mt-1"
                                        }`}
                                        color={"primary"}
                                        onClick={() =>
                                          arrayHelpers.insert(index, {
                                            id: "",
                                            location: "",
                                          })
                                        } // insert an empty string at a position
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
                                        +
                                      </Button>
                                    </div>
                                    <ErrorMessage name={`en.location.${index}`}>
                                      {(msg) => (
                                        <span style={{ color: "red" }}>
                                          {msg}
                                        </span>
                                      )}
                                    </ErrorMessage>
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
                        ></FieldArray>
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
                        <Card className={"mt-3"}>
                          <CardBody
                            style={{ boxShadow: "5px 10px 8px #888888" }}
                            className={"p-2"}
                          >
                            {previewFiles.length > 0
                              ? previewFiles.map((files, index) => (
                                  <div
                                    key={index}
                                    className={
                                      "d-flex flex-row justify-content-between"
                                    }
                                    id={`id-${index}`}
                                  >
                                    <Field
                                      type={"hidden"}
                                      id={`old_files`}
                                      name={`old_files`}
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
                                    {files.file_name}
                                    <div>
                                      <button
                                        type={"button"}
                                        className="btn btn-icon btn-sm js-sweetalert"
                                        onClick={() => {
                                          handleSelectedDivDelete(index);
                                          setDeleteRowId((current) =>
                                            current.filter((item) => {
                                              return item.id !== files.id;
                                            })
                                          );
                                        }}
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
                                        <i className="fa fa-trash-o text-danger" />
                                      </button>
                                      <a
                                        href={`${baseURL}companies/file/download/${files.id}`}
                                        target="_blank"
                                      >
                                        <button
                                          type={"button"}
                                          className="btn btn-icon btn-sm js-sweetalert"
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
                                          <i className="fa fa-download"></i>
                                        </button>
                                      </a>
                                    </div>
                                  </div>
                                ))
                              : "Ühtegi manust ei leitud"}
                          </CardBody>
                        </Card>
                      </Col>
                    </Row>
                    <Row>
                      <div className={"w-100 text-right"}>
                        <Button
                          type={"submit"}
                          color={"primary"}
                          className={"mr-2"}
                          disabled={companySelector.isLoading}
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
                          {companySelector.isLoading ? (
                            <Loader />
                          ) : (
                            <>{t("buttons.update")}</>
                          )}
                        </Button>
                        <Button
                          type={"button"}
                          color={"primary"}
                          onClick={() =>
                            history.push(`/hr-companies/view/${id}`)
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
                          <>{t("buttons.back")}</>
                        </Button>
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

export default EditCompany;
