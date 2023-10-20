import React, { useEffect, useState } from "react";
import { useHistory, useLocation, useParams } from "react-router-dom";
import { Button, Card, CardBody, CardHeader, Col, Row } from "reactstrap";
import { getUserProfile } from "../../../redux/actions/usersAction";
import {
  deleteViewCompaniesUploads,
  getAllCompanyProfileById,
  getAllCompanyRelevantJobs,
  getCompaniesCandidates,
} from "../../../redux/actions/companiesActions";
import { useDispatch, useSelector } from "react-redux";
import Toast from "../../constants/toast";
import { DataLoader } from "../../constants/loaders";
import { baseURL } from "../../Shared/baseURL";
import CompanyTimeline from "./Timeline";
import { NoListFound, NoRecordFound } from "../../constants/noRecordFound";
import { useTranslation } from "react-i18next";

const ViewCompanyDetails = (props) => {
  const { id } = useParams();
  let history = useHistory();
  let dispatch = useDispatch();
  const location = useLocation();
  const jobsDataPerRow = 3;
  const candidatesDataPerRow = 3;

  const { t } = useTranslation();

  let companyState = location.pathname;

  const [activeStatusData, setActiveStatusData] = useState([]);
  const [nextJobRow, setNextJobRow] = useState(jobsDataPerRow);
  const [nextCandidatesRow, setNextCandidatesRow] =
    useState(candidatesDataPerRow);
  const [companyById, setCompanyById] = useState([]);
  const [companyContactsById, setCompanyContactsById] = useState([]);
  const [companyLocationsById, setCompanyLocationsById] = useState([]);
  const [companyFilesById, setCompanyFilesById] = useState([]);
  const [industryById, setIndustryById] = useState([]);
  const [activeJobs, setActiveJobs] = useState([]);
  const [CandidatesData, setCandidateData] = useState();
  const [userPermission, setPermissions] = useState();

  useEffect(() => {
    window.scrollTo(0, 0);
  }, []);

  useEffect(() => {
    dispatch(getAllCompanyProfileById(id));
  }, [id]);

  useEffect(() => {
    const fetchPermissions = async () => {
      try {
        const response = await dispatch(getUserProfile());
        setPermissions(response.payload.data.permissions);
      } catch (error) {
        throw error;
      }
    };
    fetchPermissions();
  }, []);

  useEffect(() => {
    const fetCompanyJobs = async () => {
      const response = await dispatch(getAllCompanyRelevantJobs(id));
      const activeJobsFilter = response.payload.jobs.filter(
        (elem) => elem.status === 1
      );
      setActiveJobs(activeJobsFilter);
    };
    fetCompanyJobs();
  }, []);

  useEffect(() => {
    const fetchData = async () => {
      try {
        const response = await dispatch(getCompaniesCandidates(id));
        setCandidateData(response.payload.candidate);
      } catch (error) {
        throw error;
      }
    };
    fetchData();
  }, []);

  const companySelector = useSelector((state) => state.companies);
  const getCompanyProfileByIdSelector = useSelector(
    (state) => state.companies.company_profile.company
  );
  const settingsSelector = useSelector((state) => state.settings);

  useEffect(() => {
    if (getCompanyProfileByIdSelector) {
      setCompanyById(getCompanyProfileByIdSelector.company);
      setCompanyContactsById(getCompanyProfileByIdSelector.companyContacts);
      setCompanyLocationsById(getCompanyProfileByIdSelector.companylocations);
      setCompanyFilesById(getCompanyProfileByIdSelector.companyFiles);
      if (
        getCompanyProfileByIdSelector.industries.length > 0 &&
        getCompanyProfileByIdSelector.industries != null
      ) {
        setIndustryById(getCompanyProfileByIdSelector.industries);
      }
    }
  }, [getCompanyProfileByIdSelector]);

  const handleJobsDataPerRow = () => {
    setNextJobRow(nextJobRow + jobsDataPerRow);
  };

  const RenderCompanyDetails = () => {
    return (
      <div className="col-lg-12 col-md-12">
        <div className="card">
          <div className="card-header">
            <div className={"w-100 d-flex justify-content-between"}>
              <h2
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
                <strong>
                  {t("companies.addNewCompany.companyDetails.heading")}
                </strong>
              </h2>
              <button
                type="button"
                className="btn btn-primary"
                onClick={() =>
                  history.push(`/hr-companies/view/${id}/send-email`)
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
                <i className="fa fa-envelope mr-2" />
                {t("companies.sendEmailButton")}
              </button>
            </div>
          </div>
          <div className="card-body">
            {/*Can-Send-Company-Job-Link*/}
            {userPermission &&
            userPermission.includes("Can-Send-Company-Job-Link") ? (
              <div className={"w-100 d-flex justify-content-end"}>
                <button
                  type="button"
                  className="btn btn-primary"
                  onClick={() =>
                    history.push(`/hr-companies/view/${id}/send-email-link`)
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
                  <i className="fa fa-envelope mr-2" />
                  {t("companies.sendCreateRequestButton")}
                </button>
              </div>
            ) : null}
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
              <Col sm={3} md={3}>
                <div>
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
                      {t("companies.addNewCompany.companyDetails.name")}
                    </strong>
                  </h6>
                  {companyById.map((item, index) => (
                    <p key={index}>{item.name}</p>
                  ))}
                </div>
              </Col>
              <Col sm={3} md={3}>
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
                    {t("companies.addNewCompany.companyDetails.registerCode")}
                  </strong>
                </h6>
                <p>
                  {getCompanyProfileByIdSelector === undefined
                    ? ""
                    : getCompanyProfileByIdSelector.invoicing_info}
                </p>
              </Col>
              <Col sm={3} md={3}>
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
                    {t("companies.addNewCompany.companyDetails.vat")}
                  </strong>
                </h6>
                {getCompanyProfileByIdSelector === undefined
                  ? ""
                  : getCompanyProfileByIdSelector.vat}
              </Col>
            </Row>
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
            {companyContactsById.map((item, index) => (
              <Row key={index}>
                <Col sm={3} md={3}>
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
                      {t("companies.addNewCompany.personContact.name")}
                    </strong>
                  </h6>
                  <p>{item.name}</p>
                </Col>
                <Col sm={3} md={3}>
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
                      {t("companies.addNewCompany.personContact.email")}
                    </strong>
                  </h6>
                  <p>{item.email}</p>
                </Col>
                <Col sm={3} md={3}>
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
                      {t("companies.addNewCompany.personContact.phone")}
                    </strong>
                  </h6>
                  <p>{item.phone}</p>
                </Col>
                <Col sm={3} md={3}>
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
                      {t("companies.addNewCompany.personContact.position")}
                    </strong>
                  </h6>
                  <p>{item.position}</p>
                </Col>
              </Row>
            ))}
            <Row>
              <Col sm={3} md={3}>
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
                  <strong>{t("companies.addNewCompany.industry")}</strong>
                </h6>
                {industryById.map((industryItem, index) => (
                  <p key={index}>- {industryItem.name}</p>
                ))}
              </Col>
              <Col sm={3} md={3}>
                {companyLocationsById.map((item, index) => (
                  <div key={index}>
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
                      <strong>{t("companies.addNewCompany.location")}</strong>
                    </h6>
                    <p>- {item.location}</p>
                  </div>
                ))}
              </Col>
            </Row>
            <Row>
              <Col sm={4} md={4}>
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
                  <strong>{t("companies.uploads")}</strong>
                </h6>
                {companyFilesById.map((item, index) => (
                  <div key={index} className="w-100 d-flex">
                    <p className="mb-0">{item.file_name}</p>
                    <a
                      href={`${baseURL}companies/file/download/${item.id}`}
                      target="_blank"
                    >
                      <button
                        className="btn btn-icon btn-sm js-sweetalert"
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
                        <i className="fa fa-download"></i>
                      </button>
                    </a>
                    <button
                      className="btn btn-icon btn-sm js-sweetalert"
                      onClick={() => {
                        const confirm = window.confirm("Are You Sure?");
                        if (confirm === true) {
                          dispatch(deleteViewCompaniesUploads([item.id], id));
                        }
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
                  </div>
                ))}
              </Col>
            </Row>
          </div>
          <div className="card-footer">
            <div className="row">
              {
                <Button
                  type={"button"}
                  color={"primary"}
                  className={"mr-2"}
                  onClick={() => history.push(`/hr-companies/edit/${id}`)}
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
                  {t("buttons.edit")}
                </Button>
              }
            </div>
          </div>
        </div>
      </div>
    );
  };

  // const RenderActivityTimeline = () => {
  //   // const [timeline, setTimeline] = useState([]);
  //   // useEffect(() => {
  //   //   const fetchCompanyTimeline = async () => {
  //   //     try {
  //   //       const response = await dispatch(getCompanyTimeline(id));
  //   //       setTimeline(response.payload.data.timeline);
  //   //     } catch (e) {}
  //   //   };
  //   //   fetchCompanyTimeline();
  //   // }, []);
  //
  //   return (
  //     <div className="col-md-12">
  //       <div className="card">
  //         <div className="card-header">
  //           <h3 className="card-title">Timeline Activity</h3>
  //         </div>
  //         <div className="card-body">
  //           <div className="summernote">
  //             <Ckeditor />
  //           </div>
  //           <div className="timeline_item ">
  //             <img className="tl_avatar" src={AvatarImage} alt="fake_url" />
  //             <span>
  //               <a href="fake_url;">Elisse Joson</a> San Francisco, CA{" "}
  //               <small className="float-right text-right">
  //                 20-April-2019 - Today
  //               </small>
  //             </span>
  //             <h6 className="font600">
  //               Hello, 'Im a single div responsive timeline without media
  //               Queries!
  //             </h6>
  //             <div className="msg">
  //               <p>
  //                 I'm speaking with myself, number one, because I have a very
  //                 good brain and I've said a lot of things. I write the best
  //                 placeholder text, and I'm the biggest developer on the web
  //                 card she has is the Lorem card.
  //               </p>
  //               <a href="fake_url;" className="mr-20 text-muted">
  //                 <i className="fa fa-heart text-pink" /> 12 Love
  //               </a>
  //               <a
  //                 className="text-muted"
  //                 role="button"
  //                 data-toggle="collapse"
  //                 href="#collapseExample"
  //                 aria-expanded="false"
  //                 aria-controls="collapseExample"
  //               >
  //                 <i className="fa fa-comments" /> 1 Comment
  //               </a>
  //               <div
  //                 className="collapse p-4 section-gray mt-2"
  //                 id="collapseExample"
  //               >
  //                 <form className="well">
  //                   <div className="form-group">
  //                     <textarea
  //                       rows={2}
  //                       className="form-control no-resize"
  //                       placeholder="Enter here for tweet..."
  //                       defaultValue={""}
  //                     />
  //                   </div>
  //                   <button className="btn btn-primary">Submit</button>
  //                 </form>
  //                 <ul className="recent_comments list-unstyled mt-4 mb-0">
  //                   <li>
  //                     <div className="avatar_img">
  //                       <img
  //                         className="rounded img-fluid"
  //                         src={AvatarImage}
  //                         alt="fake_url"
  //                       />
  //                     </div>
  //                     <div className="comment_body">
  //                       <h6>
  //                         Donald Gardner{" "}
  //                         <small className="float-right font-14">
  //                           Just now
  //                         </small>
  //                       </h6>
  //                       <p>
  //                         Lorem ipsum Veniam aliquip culpa laboris minim tempor
  //                       </p>
  //                     </div>
  //                   </li>
  //                 </ul>
  //               </div>
  //             </div>
  //           </div>
  //           <div className="timeline_item ">
  //             <img className="tl_avatar" src={AvatarImage} alt="fake_url" />
  //             <span>
  //               <a href="fake_url">Dessie Parks</a> Oakland, CA{" "}
  //               <small className="float-right text-right">
  //                 19-April-2019 - Yesterday
  //               </small>
  //             </span>
  //             <h6 className="font600">Oeehhh, that's awesome.. Me too!</h6>
  //             <div className="msg">
  //               <p>
  //                 I'm speaking with myself, number one, because I have a very
  //                 good brain and I've said a lot of things. on the web by far...
  //                 While that's mock-ups and this is politics, are they really so
  //                 different? I think the only card she has is the Lorem card.
  //               </p>
  //               <div className="timeline_img mb-20">
  //                 <img className="width100" src={AvatarImage} alt="Awesome" />
  //                 <img className="width100" src={AvatarImage} alt="Awesome" />
  //               </div>
  //               <a href="fake_url;" className="mr-20 text-muted">
  //                 <i className="fa fa-heart text-pink" /> 23 Love
  //               </a>
  //               <a
  //                 className="text-muted"
  //                 role="button"
  //                 data-toggle="collapse"
  //                 href="#collapseExample1"
  //                 aria-expanded="false"
  //                 aria-controls="collapseExample1"
  //               >
  //                 <i className="fa fa-comments" /> 2 Comment
  //               </a>
  //               <div
  //                 className="collapse p-4 section-gray mt-2"
  //                 id="collapseExample1"
  //               >
  //                 <form className="well">
  //                   <div className="form-group">
  //                     <textarea
  //                       rows={2}
  //                       className="form-control no-resize"
  //                       placeholder="Enter here for tweet..."
  //                       defaultValue={""}
  //                     />
  //                   </div>
  //                   <button className="btn btn-primary">Submit</button>
  //                 </form>
  //                 <ul className="recent_comments list-unstyled mt-4 mb-0">
  //                   <li>
  //                     <div className="avatar_img">
  //                       <img
  //                         className="rounded img-fluid"
  //                         src={AvatarImage}
  //                         alt="fake_url"
  //                       />
  //                     </div>
  //                     <div className="comment_body">
  //                       <h6>
  //                         Donald Gardner{" "}
  //                         <small className="float-right font-14">
  //                           Just now
  //                         </small>
  //                       </h6>
  //                       <p>
  //                         Lorem ipsum Veniam aliquip culpa laboris minim tempor
  //                       </p>
  //                       <div className="timeline_img mb-20">
  //                         <img
  //                           className="width150"
  //                           src={AvatarImage}
  //                           alt="Awesome"
  //                         />
  //                         <img
  //                           className="width150"
  //                           src={AvatarImage}
  //                           alt="Awesome"
  //                         />
  //                       </div>
  //                     </div>
  //                   </li>
  //                   <li>
  //                     <div className="avatar_img">
  //                       <img
  //                         className="rounded img-fluid"
  //                         src={AvatarImage}
  //                         alt="fake_url"
  //                       />
  //                     </div>
  //                     <div className="comment_body">
  //                       <h6>
  //                         Dessie Parks{" "}
  //                         <small className="float-right font-14">
  //                           1min ago
  //                         </small>
  //                       </h6>
  //                       <p>
  //                         It is a long established fact that a reader will be
  //                         distracted by the readable content of a page when
  //                         looking
  //                       </p>
  //                     </div>
  //                   </li>
  //                 </ul>
  //               </div>
  //             </div>
  //           </div>
  //         </div>
  //       </div>
  //     </div>
  //   );
  // };

  // const RenderActiveJobs = () => {
  //   return (
  //     <div className="col-lg-12 col-md-12 col-sm-12">
  //       <div className="card">
  //         <div className="card-header">
  //           <h3 className="card-title">Available Job Offers</h3>
  //         </div>
  //         <div className="card-body">
  //           <div className="table-responsive">
  //             <table className="table table-hover table-striped table-vcenter mb-0">
  //               <thead>
  //                 <tr>
  //                   <th>TITLE</th>
  //                   <th>CREATOR</th>
  //                   <th>CREATED DATE</th>
  //                   <th>DEADLINE</th>
  //                   <th>STATUS</th>
  //                   <th>COMPANY</th>
  //                   <th>P. REQUIRED</th>
  //                   <th>P. APPLIED</th>
  //                   <th>JOB TYPE</th>
  //                   <th>LOCATION</th>
  //                   <th />
  //                 </tr>
  //               </thead>
  //               {getCompanyRelevantJobs
  //                 ? getCompanyRelevantJobs
  //                     .slice(0, nextJobRow)
  //                     .map((item, index) => (
  //                       <tbody key={index}>
  //                         <tr>
  //                           <td>
  //                             <div className="font-15">{item.title}</div>
  //                           </td>
  //                           <td>{item.creator}</td>
  //                           <td>
  //                             {item.created_at === null ? "" : item.created_at}
  //                           </td>
  //                           <td>
  //                             {item.deadline === null ? "" : item.deadline}
  //                           </td>
  //                           <td>
  //                             {item.status === 0
  //                               ? "Inactive"
  //                               : item.status === 1
  //                               ? "Active"
  //                               : ""}
  //                           </td>
  //                           <td>{item.company}</td>
  //                           <td>{item.required}</td>
  //                           <td>{item.peopleApplied}</td>
  //                           <td>{item.type_of_job}</td>
  //                           <td>{item.location}</td>
  //                           <td>
  //                             <button
  //                               type="button"
  //                               className="btn btn-icon btn-sm"
  //                               title="View"
  //                               onClick={() => history.push("/hr-jobs")}
  //                             >
  //                               <i className="fa fa-eye" />
  //                             </button>
  //                           </td>
  //                         </tr>
  //                       </tbody>
  //                     ))
  //                 : "Loading.."}
  //             </table>
  //             <div className={"w-100 text-center"}>
  //               {nextJobRow < activeStatusData.length ? (
  //                 <Button
  //                   className="mt-4"
  //                   color={"primary"}
  //                   onClick={handleJobsDataPerRow}
  //                 >
  //                   Load more
  //                 </Button>
  //               ) : (
  //                 <div className={"mt-2"}>
  //                   <h6>No More Jobs</h6>
  //                 </div>
  //               )}
  //             </div>
  //           </div>
  //         </div>
  //       </div>
  //     </div>
  //   );
  // };

  const handleCandidateDataPerRow = () => {
    setNextCandidatesRow(nextCandidatesRow + candidatesDataPerRow);
  };

  const RenderCandidatesList = () => {
    return (
      <Col lg={12} md={12} sm={12}>
        <Card>
          <CardHeader>
            <h2
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
              Kandidaatide nimekiri
            </h2>
          </CardHeader>
          <CardBody>
            <div className="table-responsive">
              <table className="table table-hover table-striped table-vcenter mb-0">
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
                      Nimi
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
                      töökoha nimi
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
                      Telefon
                    </th>
                    {/*<th>Renditöö vajadus</th>*/}
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
                      Faas
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
                      Kandideerimise kuupäev
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
                      Staatus
                    </th>
                    <th />
                  </tr>
                </thead>
                <tbody>
                  {!companySelector.isLoading ? (
                    CandidatesData && CandidatesData.length > 0 ? (
                      CandidatesData.slice(0, nextCandidatesRow).map(
                        (item, index) => (
                          <tr key={index}>
                            <td
                              onClick={() =>
                                history.push({
                                  pathname: `/hr-candidate/view/${item.candidatecv_id}/1`,
                                  state: {
                                    userPermission,
                                    companyState,
                                    update: true,
                                  },
                                })
                              }
                              style={{ cursor: "pointer" }}
                            >
                              {/* <div
                                  className="font-15"
                                  style={{
                                    fontSize: `${
                                      settingsSelector.FontSize === "Large"
                                        ? "large"
                                        : settingsSelector.FontSize ===
                                          "Extra Large"
                                        ? "x-large"
                                        : "15px"
                                    }`,
                                  }}
                                > */}
                              {item.first_name} {item.last_name}
                              {/* </div> */}
                            </td>
                            <td
                              onClick={() =>
                                history.push({
                                  pathname: `/hr-candidate/view/${item.candidatecv_id}/1`,
                                  state: { userPermission, companyState },
                                })
                              }
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
                            >
                              {item.offer_name}
                            </td>
                            <td
                              onClick={() =>
                                history.push({
                                  pathname: `/hr-candidate/view/${item.candidatecv_id}/1`,
                                  state: { userPermission, companyState },
                                })
                              }
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
                            >
                              {item.phone}
                            </td>
                            {/*<td*/}
                            {/*  onClick={() =>*/}
                            {/*    history.push({*/}
                            {/*      pathname: `/hr-candidate/view/${item.id}`,*/}
                            {/*      state: userPermission,*/}
                            {/*    })*/}
                            {/*  }*/}
                            {/*  style={{ cursor: "pointer" }}*/}
                            {/*>*/}
                            {/*  {item.phone}>{item.type_of_job}*/}
                            {/*</td>*/}
                            <td
                              onClick={() =>
                                history.push({
                                  pathname: `/hr-candidate/view/${item.candidatecv_id}/1`,
                                  state: { userPermission, companyState },
                                })
                              }
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
                            >
                              {item.action_name}
                            </td>
                            <td
                              onClick={() =>
                                history.push({
                                  pathname: `/hr-candidate/view/${item.candidatecv_id}/1`,
                                  state: { userPermission, companyState },
                                })
                              }
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
                            >
                              {item.applied_date.substring(0, 10)}
                            </td>
                            <td
                              onClick={() =>
                                history.push({
                                  pathname: `/hr-candidate/view/${item.candidatecv_id}/1`,
                                  state: { userPermission, companyState },
                                })
                              }
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
                            >
                              {item.status == 1 ? "aktiivne" : "mitteaktiivne"}
                            </td>
                            <td>
                              <button
                                type="button"
                                className="btn btn-icon btn-sm"
                                title="View"
                                onClick={() =>
                                  history.push({
                                    pathname: `/hr-candidate/view/${item.candidatecv_id}/1`,
                                    state: { userPermission, companyState },
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
                            </td>
                          </tr>
                        )
                      )
                    ) : (
                      <tr>
                        <td>
                          <NoRecordFound />
                        </td>
                      </tr>
                    )
                  ) : (
                    <tr>
                      <td>
                        <DataLoader />
                      </td>
                    </tr>
                  )}
                </tbody>
              </table>
              <div className={"w-100 text-center"}>
                {CandidatesData &&
                  nextCandidatesRow < CandidatesData.length && (
                    <Button
                      className="mt-4"
                      color={"primary"}
                      onClick={handleCandidateDataPerRow}
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
                      Lae veel juurde
                    </Button>
                  )}
              </div>
            </div>
          </CardBody>
        </Card>
      </Col>
    );
  };

  return (
    <div className="container-fluid mt-3">
      <Row className={"w-100 d-flex mb-2 justify-content-end"}>
        <Button
          type={"button"}
          color={"primary"}
          onClick={() => history.push(`/hr-companies`)}
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
          Tagasi
        </Button>
      </Row>
      <RenderCompanyDetails />
      <CompanyTimeline id={id} />
      <div className="col-lg-12 col-md-12 col-sm-12">
        <div className="card">
          <div className="card-header">
            <h2
              // className="card-title"
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
              Olemasolev tööpakkumised
            </h2>
          </div>
          <div className="card-body">
            <div className="table-responsive">
              <table className="table table-hover table-striped table-vcenter mb-0">
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
                      Nimi
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
                      Looja
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
                      Loomise kuupäev
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
                      Tähtaeg
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
                      Staatus
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
                      Ettevõte
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
                      Otsitavate inimeste hulk
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
                      Renditöö vajadus
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
                      Asukoht
                    </th>
                    <th />
                  </tr>
                </thead>
                <tbody>
                  {!companySelector.isLoading ? (
                    activeJobs.length > 0 ? (
                      activeJobs.slice(0, nextJobRow).map((item, index) => (
                        <tr key={index}>
                          <td
                            style={{
                              cursor: "pointer",
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                            }}
                            onClick={() =>
                              history.push({
                                pathname: `/hr-jobs/view/${item.id}`,
                                state: userPermission,
                              })
                            }
                          >
                            {/* <div
                              className="font-15"
                              style={{
                                fontSize: `${
                                  settingsSelector.FontSize === "Large"
                                    ? "large"
                                    : settingsSelector.FontSize ===
                                      "Extra Large"
                                    ? "x-large"
                                    : "15px"
                                }`,
                              }}
                            > */}
                            {item.jobs[0] ? item.jobs[0].offer_name : ""}
                            {/* </div> */}
                          </td>
                          <td
                            style={{
                              cursor: "pointer",
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                            }}
                            onClick={() =>
                              history.push({
                                pathname: `/hr-jobs/view/${item.id}`,
                                state: userPermission,
                              })
                            }
                          >
                            {item.creator}
                          </td>
                          <td
                            style={{
                              cursor: "pointer",
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                            }}
                            onClick={() =>
                              history.push({
                                pathname: `/hr-jobs/view/${item.id}`,
                                state: userPermission,
                              })
                            }
                          >
                            {item.created_at === null
                              ? ""
                              : item.created_at.substring(0, 10)}
                          </td>
                          <td
                            style={{
                              cursor: "pointer",
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                            }}
                            onClick={() =>
                              history.push({
                                pathname: `/hr-jobs/view/${item.id}`,
                                state: userPermission,
                              })
                            }
                          >
                            {item.deadline === null ? "" : item.deadline}
                          </td>
                          <td
                            style={{
                              cursor: "pointer",
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                            }}
                            onClick={() =>
                              history.push({
                                pathname: `/hr-jobs/view/${item.id}`,
                                state: userPermission,
                              })
                            }
                          >
                            {item.status === 1 ? "Active" : ""}
                          </td>
                          <td
                            style={{
                              cursor: "pointer",
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                            }}
                            onClick={() =>
                              history.push({
                                pathname: `/hr-jobs/view/${item.id}`,
                                state: userPermission,
                              })
                            }
                          >
                            {companyById.map((item, index) => (
                              <div key={index}>{item.name}</div>
                            ))}
                          </td>
                          <td
                            style={{
                              cursor: "pointer",
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                            }}
                            onClick={() =>
                              history.push({
                                pathname: `/hr-jobs/view/${item.id}`,
                                state: userPermission,
                              })
                            }
                          >
                            {item.required_candidates}
                          </td>
                          <td
                            style={{
                              cursor: "pointer",
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                            }}
                            onClick={() =>
                              history.push({
                                pathname: `/hr-jobs/view/${item.id}`,
                                state: userPermission,
                              })
                            }
                          >
                            {item.job_type}
                          </td>
                          <td
                            style={{
                              cursor: "pointer",
                              fontSize: `${
                                settingsSelector.FontSize === "Large"
                                  ? "large"
                                  : settingsSelector.FontSize === "Extra Large"
                                  ? "x-large"
                                  : "14px"
                              }`,
                            }}
                            onClick={() =>
                              history.push({
                                pathname: `/hr-jobs/view/${item.id}`,
                                state: userPermission,
                              })
                            }
                          >
                            {item.jobs[0] ? item.jobs[0].location : ""}
                          </td>
                          <td>
                            <button
                              type="button"
                              className="btn btn-icon btn-sm"
                              title="View"
                              onClick={() =>
                                history.push(`/hr-jobs/view/${item.id}`)
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
                          </td>
                        </tr>
                      ))
                    ) : (
                      <tr>
                        <td>
                          <NoListFound />
                        </td>
                      </tr>
                    )
                  ) : (
                    <tr>
                      <td>
                        <DataLoader />
                      </td>
                    </tr>
                  )}
                </tbody>
              </table>
              <div className={"w-100 text-center"}>
                {activeJobs.length < 4 ? (
                  ""
                ) : (
                  <>
                    {nextJobRow < activeStatusData.length ? (
                      <Button
                        className="mt-4 mb-2"
                        color={"primary"}
                        onClick={handleJobsDataPerRow}
                        disabled={activeJobs.length === 0}
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
                        Lae veel juurde
                      </Button>
                    ) : (
                      <div className={"mt-2"}>
                        <h6
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
                          Tööpakkumised otsas
                        </h6>
                      </div>
                    )}
                  </>
                )}
              </div>
            </div>
          </div>
        </div>
      </div>
      <RenderCandidatesList />
      <Toast />
    </div>
  );
};

export default ViewCompanyDetails;
