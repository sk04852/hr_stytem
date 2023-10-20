import React, { useEffect, useState } from "react";
import {
  Card,
  CardBody,
  CardHeader,
  CardTitle,
  Collapse,
  FormGroup,
} from "reactstrap";
import { useDispatch, useSelector } from "react-redux";
import { getCompanyTimeline } from "../../../redux/actions/companiesActions";
import { DataLoader, Loader } from "../../constants/loaders";
import { Button } from "reactstrap";
import { useParams } from "react-router-dom";
import { postTimelineComment } from "../../../redux/actions/timelineActions";
import { Field, Form, Formik } from "formik";

const CompanyTimeline = (props) => {
  const { id } = useParams();
  const companyDataPerRow = 3;
  const dispatch = useDispatch();

  const timelineCommentsInitialValues = {
    timeline_id: "",
    body: "",
  };

  const [timelineCommentsFormValues, setTimelineCommentsFormValues] = useState(
    timelineCommentsInitialValues
  );

  const [nextCompanyRow, setNextCompanyRow] = useState(companyDataPerRow);
  const [timelinedata, setTimelineData] = useState("");
  const [sortingOrder, setSortingOrder] = useState("asc");
  const [open, setOpen] = React.useState({});

  const handleCompanyDataPerRow = () => {
    setNextCompanyRow(nextCompanyRow + companyDataPerRow);
  };

  const fetchData = async () => {
    try {
      const response = await dispatch(getCompanyTimeline(id));
      setTimelineData(response.payload.timeline);
    } catch (error) {
      throw error;
    }
  };

  useEffect(() => {
    fetchData();
  }, []);

  const companySelector = useSelector((state) => state.companies);
  const timelineSelector = useSelector((state) => state.timeline);
  const settingsSelector = useSelector((state) => state.settings);

  function handleClick(id) {
    setOpen((prevState) => ({ ...prevState, [id]: !prevState[id] }));
  }

  const handleSorting = (e) => {
    if (sortingOrder === "asc") {
      const sorted = [...timelinedata].sort((a, b) =>
        a[e].toLowerCase() > b[e].toLowerCase() ? 1 : -1
      );
      setTimelineData(sorted);
      setSortingOrder("dsc");
    }
    if (sortingOrder === "dsc") {
      const sorted = [...timelinedata].sort((a, b) =>
        a[e].toLowerCase() < b[e].toLowerCase() ? 1 : -1
      );
      setTimelineData(sorted);
      setSortingOrder("asc");
    }
  };

  const handleTimelineCommentSubmit = (values) => {
    let element = document.getElementsByName("timeline_id");
    values["timeline_id"] = element[0].value;
    dispatch(postTimelineComment(values, id, fetchData));
  };

  return (
    <>
      <div style={{ padding: "0px 8px 0px 8px" }}>
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
                Ajalugu
              </h5>
            </CardTitle>
            <div></div>
          </CardHeader>
          <CardBody>
            <table className={"table  mb-2 table-sortable"}>
              <thead>
                <tr>
                  <th
                    onClick={() => handleSorting("name")}
                    className={sortingOrder === "asc" ? "asc" : "dsc"}
                    style={{
                      paddingRight: "50px",
                      cursor: "pointer",
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
                    onClick={() => handleSorting("updated_at")}
                    className={sortingOrder === "asc" ? "asc" : "dsc"}
                    style={{
                      paddingRight: "50px",
                      cursor: "pointer",
                      fontSize: `${
                        settingsSelector.FontSize === "Large"
                          ? "large"
                          : settingsSelector.FontSize === "Extra Large"
                          ? "x-large"
                          : "14px"
                      }`,
                    }}
                  >
                    Kuupäev
                  </th>
                </tr>
              </thead>
            </table>
            {!companySelector.isLoading ? (
              <>
                {timelinedata &&
                  timelinedata.slice(0, nextCompanyRow).map((c, index) => (
                    <div key={index} className="timeline_item ">
                      <span>
                        <a>{c && c.body}</a>
                        <small
                          className="float-right text-right"
                          style={{
                            fontSize: `${
                              settingsSelector.FontSize === "Large"
                                ? "large"
                                : settingsSelector.FontSize === "Extra Large"
                                ? "x-large"
                                : "80%"
                            }`,
                          }}
                        >
                          {c &&
                            c.created_at.replace("T", " ").substring(11, 16) +
                              " " +
                              c.created_at.replace("T", " ").substring(8, 10) +
                              "-" +
                              c.created_at.replace("T", " ").substring(5, 7) +
                              "-" +
                              c.created_at.replace("T", " ").substring(0, 4)}
                        </small>
                      </span>
                      <h6
                        className="font600"
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
                        {c.userPr === null ? "" : c.userPr.user.name}
                      </h6>
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
                        {c && c.title}
                      </h6>
                      {c.detail_description !== null && (
                        <div
                          dangerouslySetInnerHTML={{
                            __html: c.detail_description,
                          }}
                        />
                      )}
                      <div className="msg">
                        <a
                          onClick={() => handleClick(index)}
                          className="clickable text-muted"
                          role="button"
                        >
                          <i className="clickable fa fa-comments"></i>
                          {c.comments.length > 0
                            ? "View Description"
                            : "Add Description"}
                        </a>
                        <Collapse isOpen={open[index]}>
                          <div>
                            <Formik
                              initialValues={timelineCommentsFormValues}
                              onSubmit={(values) =>
                                handleTimelineCommentSubmit(values)
                              }
                            >
                              <div className="collapse-event">
                                {c.comments.map((item, index) => (
                                  <p key={index}>{item.body}</p>
                                ))}
                                <small
                                  className="float-right font-14"
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
                                  {c.comments.length > 0 ? (
                                    <>
                                      {c.comments.map((item, index) => (
                                        <div key={index}>
                                          {item.created_at
                                            .replace("T", " ")
                                            .substring(11, 16) +
                                            " " +
                                            item.created_at
                                              .replace("T", " ")
                                              .substring(8, 10) +
                                            "-" +
                                            item.created_at
                                              .replace("T", " ")
                                              .substring(5, 7) +
                                            "-" +
                                            item.created_at
                                              .replace("T", " ")
                                              .substring(0, 4)}
                                        </div>
                                      ))}
                                    </>
                                  ) : (
                                    ""
                                  )}
                                </small>
                                {c.comments.length > 0 ? null : (
                                  <Form className="well">
                                    <FormGroup>
                                      <Field
                                        type={"hidden"}
                                        name={"timeline_id"}
                                        value={c.id}
                                      />
                                      <Field
                                        as={"textarea"}
                                        rows="2"
                                        name="body"
                                        className="form-control no-resize"
                                        placeholder="Enter comment..."
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
                                    </FormGroup>
                                    <Button
                                      className="btn btn-primary"
                                      type="submit"
                                      disabled={timelineSelector.isLoading}
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
                                      {timelineSelector.isLoading ? (
                                        <Loader />
                                      ) : (
                                        "Salvesta"
                                      )}
                                    </Button>
                                  </Form>
                                )}
                              </div>
                            </Formik>
                          </div>
                        </Collapse>
                      </div>
                    </div>
                  ))}
              </>
            ) : (
              <DataLoader />
            )}
            <div className={"w-100 text-center"}>
              {timelinedata && nextCompanyRow < timelinedata.length && (
                <Button
                  className="mt-4"
                  color={"primary"}
                  onClick={handleCompanyDataPerRow}
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
          </CardBody>
        </Card>
      </div>
    </>
  );
};

export default CompanyTimeline;
