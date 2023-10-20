import React, { useCallback, useEffect, useState } from "react";
import { useHistory } from "react-router-dom";
import { useDispatch, useSelector } from "react-redux";
import {
  deleteCompanies,
  getAllCompanies,
} from "../../../redux/actions/companiesActions";
import { DataLoader } from "../../constants/loaders";
import Toast from "../../constants/toast";
import {
  Button,
  Card,
  CardBody,
  CardHeader,
  CardTitle,
  Col,
  Container,
  Dropdown,
  FormGroup,
  Input,
  Label,
  Row,
} from "reactstrap";
import Paginator from "react-hooks-paginator";
import { NoListFound, NoRecordFound } from "../../constants/noRecordFound";
import { useTranslation } from "react-i18next";

const Companies = (props) => {
  const { fixNavbar } = props;
  let history = useHistory();
  let dispatch = useDispatch();

  const { t } = useTranslation();

  const initialValues = {
    company_name: "",
    industry: "",
    company_location: "",
  };

  const [formValues, setFormValues] = useState(initialValues);
  const [data, setData] = useState([]);
  const [searchTerm, setSearchTerm] = useState("");
  const [sortingOrder, setSortingOrder] = useState("asc");
  const [pageNumber, setPageNumber] = useState(1);
  const [totalRecords, setTotalRecords] = useState(0);
  const [offset, setOffset] = useState(0);
  const [deleteID, setDeleteID] = useState([]);
  const [companyNameFilter, setCompanyNameFilter] = useState([]);
  const [companyIndustryFilter, setCompanyIndustryFilter] = useState([]);
  const [userPermission, setUserPermission] = useState([]);
  const [sorting, setSorting] = useState(null);

  const fetchCompanies = async (
    companyName,
    companyIndustry,
    companyLocation
  ) => {
    try {
      const response = await dispatch(
        getAllCompanies(
          pageNumber,
          searchTerm,
          companyName,
          companyIndustry,
          companyLocation,
          sorting
        )
      );

      let companyData = response.payload.data;

      if (companyData.length === 0) {
        setData([]);
      } else {
        setData(companyData.data);
        setTotalRecords(companyData.total);
      }

      if (companyData?.data?.length > 0) {
        let companiesNameArr = [...companyNameFilter];
        companyData.data.map((item) => {
          companiesNameArr.push(item.name);
        });
        let uniqueCompanyName = [...new Set(companiesNameArr)];
        setCompanyNameFilter(uniqueCompanyName);

        let companyIndustryArr = [...companyIndustryFilter];
        companyData.data.map((item) => {
          if (item.industries.length > 0) {
            item.industries.map((industryItem) => {
              companyIndustryArr.push({
                id: industryItem.id,
                name: industryItem.name,
              });
            });
          }
        });
        let uniqueCompanyIndustry = [...new Set(companyIndustryArr)];

        setCompanyIndustryFilter(uniqueCompanyIndustry);
      }
    } catch (e) {
      throw e;
    }
  };

  useEffect(() => {
    fetchCompanies();
  }, [pageNumber, searchTerm, sorting]);

  const companiesSelector = useSelector((state) => state.companies);
  const getUserProfileSelector = useSelector(
    (state) => state.users.user_profile.data
  );
  const settingsSelector = useSelector((state) => state.settings);

  useEffect(() => {
    if (getUserProfileSelector) {
      setUserPermission(getUserProfileSelector.permissions);
    }
  }, [getUserProfileSelector]);

  const handleSorting = (e) => {
    if (sortingOrder === "asc") {
      setSortingOrder("dsc");
      setSorting("-" + e);
    }
    if (sortingOrder === "dsc") {
      setSortingOrder("asc");
      setSorting(e);
    }
  };

  const resetFilters = () => {
    let elements = document.getElementById("selectFilters");
    elements.selectedIndex = 0;
    let elements2 = document.getElementById("selectFilters1");
    elements2.selectedIndex = 0;
    let elements3 = document.getElementById("selectFilters2");
    elements3.selectedIndex = 0;

    setFormValues(initialValues);
    setData(fetchCompanies("", "", ""));
  };

  const handleDelete = (e, id) => {
    // deleteID = [id];
    const confirm = window.confirm("Are You Sure?");
    if (confirm === true) {
      dispatch(deleteCompanies([id], setData, fetchCompanies));
    }
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

  const handleSearchChange = (e) => {
    let searchValue = e.target.value;
    if (searchValue.length) {
      setSearchTerm(searchValue);
    } else if (searchValue.length === 0) {
      setSearchTerm("");
    }
  };

  const optimizedSearchFn = useCallback(debounce(handleSearchChange), []);

  return (
    <>
      <div className={`section-body ${fixNavbar ? "marginTop" : ""} `}>
        <Container fluid>
          <div className="d-flex justify-content-between align-items-center mb-3">
            {userPermission.includes("Can-Add-Companies") ? (
              <div className="header-action w-100 text-right mt-2">
                <button
                  type="button"
                  className="btn btn-primary"
                  data-toggle="modal"
                  data-target="#exampleModal"
                  onClick={() => history.push("/hr-companies/add-new-company")}
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
                  {t("companies.addNewCompanyButton")}
                </button>
              </div>
            ) : null}
          </div>
          <Row className={"clearfix"}>
            <Col className="col-12">
              <Card>
                <CardBody>
                  <Row>
                    <Col lg={2} md={4} sm={6}>
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
                        {t("companies.addNewCompany.companyDetails.name")}
                      </Label>
                      <div className="multiselect_div">
                        <select
                          className="custom-select"
                          id={"selectFilters"}
                          onChange={(e) =>
                            setFormValues({
                              ...formValues,
                              company_name: e.target.value,
                            })
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
                          <option value={""}>Ei ole valitud</option>
                          {companyNameFilter.map((item, index) => (
                            <option value={item} key={index}>
                              {item}
                            </option>
                          ))}
                        </select>
                      </div>
                    </Col>
                    <Col lg={2} md={4} sm={6}>
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
                        {t("companies.addNewCompany.industry")}
                      </Label>
                      <FormGroup>
                        <select
                          className="custom-select"
                          id={"selectFilters1"}
                          onChange={(e) =>
                            setFormValues({
                              ...formValues,
                              industry: e.target.value,
                            })
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
                          <option value={""}>Ei ole valitud</option>
                          {companyIndustryFilter.map((item, index) => (
                            <option value={item.id} key={index}>
                              {item.name}
                            </option>
                          ))}
                        </select>
                      </FormGroup>
                    </Col>
                    <Col lg={2} md={4} sm={6}>
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
                        {t("companies.addNewCompany.location")}
                      </Label>
                      <FormGroup className="form-group">
                        <Input
                          className="form-control"
                          id={"selectFilters2"}
                          onChange={(e) =>
                            setFormValues({
                              ...formValues,
                              company_location: e.target.value,
                            })
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
                        />
                      </FormGroup>
                    </Col>
                    <Col lg={2} md={4} sm={6}>
                      <Label>&nbsp;</Label>
                      <Button
                        type={"button"}
                        className="btn btn-sm btn-primary btn-block"
                        onClick={() =>
                          fetchCompanies(
                            formValues.company_name,
                            formValues.industry,
                            formValues.company_location
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
                        {t("buttons.filter")}
                      </Button>
                    </Col>
                    <Col lg={2} md={4} sm={6}>
                      <Label>&nbsp;</Label>
                      <Button
                        type={"reset"}
                        className="btn btn-sm btn-primary btn-block"
                        onClick={resetFilters}
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
                        {t("buttons.reset")}
                      </Button>
                    </Col>
                  </Row>
                </CardBody>
              </Card>
            </Col>
          </Row>
        </Container>
      </div>
      <div className="section-body">
        <Container fluid>
          <div className="tab-content">
            <div
              className="tab-pane fade show active"
              id="Employee-list"
              role="tabpanel"
            >
              <Card>
                <CardHeader>
                  <CardTitle>
                    <h5
                      style={{
                        fontSize: `${
                          settingsSelector.FontSize === "Large"
                            ? "1.75rem"
                            : settingsSelector.FontSize === "Extra Large"
                            ? "2.25rem"
                            : "1.25rem"
                        }`,
                      }}
                    >
                      {t("sideMenu.company")}
                    </h5>
                  </CardTitle>
                  <div className="card-options">
                    <form>
                      <div className="input-group">
                        <Input
                          type="text"
                          className="form-control form-control-sm"
                          placeholder="Search something..."
                          name="s"
                          onChange={optimizedSearchFn}
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
                      </div>
                    </form>
                  </div>
                </CardHeader>
                <CardBody>
                  <div className="table-responsive">
                    <table
                      id={"candidate-table"}
                      className="table table-hover table-striped table-vcenter text-nowrap mb-0 table-sortable"
                    >
                      <thead>
                        <tr>
                          <th
                            className={"text-center mb-0 no-sort"}
                            style={{
                              color: "#000",
                              marginTop: "5px",
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                            }}
                          >
                            #
                          </th>
                          <th
                            onClick={() => handleSorting("name")}
                            className={sortingOrder === "asc" ? "asc" : "dsc"}
                            style={{
                              paddingRight: "50px",
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                            }}
                          >
                            {t("companies.addNewCompany.companyDetails.name")}
                          </th>
                          <th
                            className={`no-sort`}
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
                            {t("companies.addNewCompany.personContact.phone")}
                          </th>
                          <th
                            // onClick={() => handleSorting("industry")}
                            // className={sortingOrder === "asc" ? "asc" : "dsc"}
                            className="no-sort"
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
                            {/* industries */}
                            {t("companies.addNewCompany.industry")}
                          </th>
                          <th
                            // onClick={() => handleSorting("companyLocation")}
                            // className={sortingOrder === "asc" ? "asc" : "dsc"}
                            className="no-sort"
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
                            {t("companies.addNewCompany.location")}
                          </th>
                          <th
                            className={`no-sort`}
                            style={{
                              // color: "#2c2945",
                              // marginTop: "5px",
                              // padding: "0 8px",
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
                        {!companiesSelector.isLoading ? (
                          <>
                            {data !== undefined && data.length > 0 ? (
                              data.map((c, index) => (
                                <tr key={index}>
                                  <td className="w40">
                                    <label
                                      className="custom-control custom-checkbox"
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
                                      <Input
                                        type="checkbox"
                                        className="custom-control-input"
                                        name="example-checkbox1"
                                        defaultValue="option1"
                                      />
                                      <span className="custom-control-label">
                                        &nbsp;
                                      </span>
                                    </label>
                                  </td>
                                  <td
                                    key={index}
                                    style={{
                                      cursor: "pointer",
                                    }}
                                    onClick={() =>
                                      history.push({
                                        pathname: `/hr-companies/view/${c.id}`,
                                        state: userPermission,
                                      })
                                    }
                                  >
                                    <h6
                                      className="mb-0"
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
                                      {c.name}
                                    </h6>
                                  </td>
                                  <td
                                    style={{
                                      cursor: "pointer",
                                    }}
                                    onClick={() =>
                                      history.push({
                                        pathname: `/hr-companies/view/${c.id}`,
                                        state: userPermission,
                                      })
                                    }
                                  >
                                    {c.companyContacts.map(
                                      (companyContact, index) => (
                                        <div key={index}>
                                          <span
                                            className={`${
                                              c.companyContacts.length > 1
                                                ? "border-bottom"
                                                : ""
                                            } d-inline-block`}
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
                                            {companyContact.phone}
                                          </span>
                                        </div>
                                      )
                                    )}
                                  </td>
                                  <td
                                    style={{
                                      cursor: "pointer",
                                      fontSize: `${
                                        settingsSelector.FontSize === "Large"
                                          ? "large"
                                          : settingsSelector.FontSize ===
                                            "Extra Large"
                                          ? "x-large"
                                          : "14px"
                                      }`,
                                    }}
                                    onClick={() =>
                                      history.push({
                                        pathname: `/hr-companies/view/${c.id}`,
                                        state: userPermission,
                                      })
                                    }
                                  >
                                    {c.industries.length > 0
                                      ? c.industries.map(
                                          (industryName, index) => (
                                            <div key={index}>
                                              <span
                                                className={`${
                                                  c.industries.length > 1
                                                    ? "border-bottom"
                                                    : ""
                                                } d-inline-block text-truncate`}
                                                style={{ maxWidth: "200px" }}
                                              >
                                                {industryName.name}
                                              </span>
                                              <br />
                                            </div>
                                          )
                                        )
                                      : ""}
                                  </td>
                                  <td
                                    style={{
                                      cursor: "pointer",
                                      fontSize: `${
                                        settingsSelector.FontSize === "Large"
                                          ? "large"
                                          : settingsSelector.FontSize ===
                                            "Extra Large"
                                          ? "x-large"
                                          : "14px"
                                      }`,
                                    }}
                                    onClick={() =>
                                      history.push({
                                        pathname: `/hr-companies/view/${c.id}`,
                                        state: userPermission,
                                      })
                                    }
                                  >
                                    {c.companylocations.map(
                                      (companyLocation, index) => (
                                        <div key={index}>
                                          <span
                                            className={`${
                                              c.companylocations.length > 1
                                                ? "border-bottom"
                                                : ""
                                            } d-inline-block text-truncate`}
                                            style={{ maxWidth: "200px" }}
                                          >
                                            {companyLocation.location}
                                          </span>
                                        </div>
                                      )
                                    )}
                                  </td>
                                  <td>
                                    <button
                                      type="button"
                                      className="btn btn-icon btn-sm"
                                      title="View"
                                      onClick={() =>
                                        history.push({
                                          pathname: `/hr-companies/view/${c.id}`,
                                          state: userPermission,
                                        })
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
                                      <i className="fa fa-eye" />
                                    </button>
                                    {userPermission.includes(
                                      "Can-Delete-Companies"
                                    ) ? (
                                      <button
                                        type="button"
                                        className="btn btn-icon btn-sm js-sweetalert"
                                        title="Delete"
                                        data-type="confirm"
                                        onClick={(e) => handleDelete(e, c.id)}
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
                                    ) : null}
                                  </td>
                                </tr>
                              ))
                            ) : (
                              <tr>
                                <td>
                                  <NoListFound />
                                </td>
                              </tr>
                            )}
                          </>
                        ) : (
                          <tr>
                            <td>
                              <DataLoader />
                            </td>
                          </tr>
                        )}
                      </tbody>
                    </table>
                  </div>
                </CardBody>
                <Paginator
                  totalRecords={totalRecords}
                  pageLimit={10}
                  pageNeighbours={2}
                  setOffset={setOffset}
                  currentPage={pageNumber}
                  setCurrentPage={setPageNumber}
                />
              </Card>
            </div>
          </div>
        </Container>
      </div>
      <Toast />
    </>
  );
};

export default Companies;
