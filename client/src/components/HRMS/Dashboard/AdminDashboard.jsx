import React, { useEffect, useState } from "react";
import { Card, CardBody, Col, Container, Row, Toast } from "reactstrap";
import { getUserProfile } from "../../../redux/actions/usersAction";
import { useDispatch, useSelector } from "react-redux";
import { Link, useHistory, useLocation } from "react-router-dom";
import DashboardTasks from "./DashboardTasks";
import {
  getAllCreatedTasks,
  getAllTasksCount,
} from "../../../redux/actions/taskManagementActions";
import Taskboard from "../TaskManagement/Taskboard";
import { useTranslation } from "react-i18next";

const AdminDashboard = (props) => {
  const { fixNavbar } = props;
  const dispatch = useDispatch();
  const history = useHistory();
  const location = useLocation();

  const { t } = useTranslation();

  const [userProfile, setUserProfile] = useState("");
  const [tasksCount, setTaskCount] = useState("");
  const [assignedTaskCounts, setAssignedTaskCounts] = useState("");
  const [showComponent, setShowComponent] = useState(false);
  const [showTaskboardComponent, setShowTaskboardComponent] = useState(false);
  const [tabID, setTabID] = useState("task");
  const [pageNumber, setPageNumber] = useState(1);
  const [userRole, setUserRole] = useState([]);

  const setBookMart = (tabID) => {
    history.push(`/dashboard/${tabID}`);
    setTabID(tabID);
  };

  const toggleTaskComponent = () => {
    setShowComponent(!showComponent);
  };

  const toggleTaskBoardComponent = () => {
    setShowTaskboardComponent(!showTaskboardComponent);
  };

  const fetchUserProfile = async () => {
    try {
      const response = await dispatch(getUserProfile());
      let roles = [];
      response.payload.data.roles.map((item) => {
        roles.push(item.name);
      });
      setUserRole(roles);
    } catch (error) {
      throw error;
    }
  };

  useEffect(() => {
    fetchUserProfile();
  }, []);

  useEffect(() => {
    dispatch(getAllCreatedTasks(pageNumber));
  }, [pageNumber]);

  useEffect(() => {
    const fetchAllTaskCounts = async () => {
      try {
        const response = await dispatch(getAllTasksCount());
        // let created_task_counts =
        //   response.payload.data.statistics.created_tasks_counts;
        let assigned_task_counts =
          response.payload.data.statistics.assigned_tasks_counts;
        setAssignedTaskCounts(assigned_task_counts);
      } catch (error) {
        throw error;
      }
    };
    fetchAllTaskCounts();
  }, []);

  const userProfileSelector = useSelector(
    (state) => state.users.user_profile.data
  );

  useEffect(() => {
    if (userProfileSelector) {
      setUserProfile(userProfileSelector);
    }
  }, [userProfileSelector]);

  const getAllTasksSelector = useSelector(
    (state) => state.tasks.all_created_tasks
  );

  useEffect(() => {
    if (getAllTasksSelector.data) {
      setTaskCount(getAllTasksSelector.total);
    }
  }, [getAllTasksSelector.data]);

  return (
    <>
      <div className={`section-body ${fixNavbar ? "marginTop" : ""} mt-3`}>
        <Container fluid={true}>
          <Row className="clearfix">
            <Col lg={12}>
              <div
                className={`section-body ${fixNavbar ? "mb-4 mt-3" : "mb-4"}`}
              >
                {/* <h4>Welcome {userProfile.name}!</h4> */}
                <h4>
                  {t("welcome text")} {userProfile.name}!
                </h4>
                {/* <small>
                  Measure How Fast You’re Growing Monthly Recurring Revenue.{" "}
                  <a href="/#">Learn More</a>
                </small> */}
              </div>
            </Col>
          </Row>
          <Row className="clearfix">
            <Col md={4} xl={2} className="col-6">
              <Card>
                <CardBody className="ribbon">
                  <div className="ribbon-box green">
                    {tasksCount === ""
                      ? "0"
                      : tasksCount >= 10
                      ? "10+"
                      : tasksCount}
                  </div>
                  <Link
                    to="#"
                    className="my_sort_cut text-muted"
                    onClick={() => {
                      setBookMart("task");
                      toggleTaskComponent();
                    }}
                  >
                    <i className="icon-list" />
                    <span>{t("dashboard.taskboard")}</span>
                    {/* <span>Ülesanded</span> */}
                  </Link>
                </CardBody>
              </Card>
            </Col>
            <Col md={4} xl={2} className="col-6">
              <Card>
                <CardBody className="ribbon">
                  <div className="ribbon-box pink">{assignedTaskCounts}</div>
                  <Link
                    to="#"
                    className="my_sort_cut text-muted"
                    onClick={() => {
                      setBookMart("todo");
                      toggleTaskBoardComponent();
                    }}
                  >
                    <i className="icon-like" />
                    <span>{t("dashboard.todo")}</span>
                  </Link>
                </CardBody>
              </Card>
            </Col>
            <Col md={4} xl={2} className="col-6">
              <Card>
                <CardBody>
                  <Link to="/hr-payroll" className="my_sort_cut text-muted">
                    <i className="icon-credit-card" />
                    <span>{t("dashboard.payroll")}</span>
                  </Link>
                </CardBody>
              </Card>
            </Col>
            <Col md={4} xl={2} className="col-6">
              <Card>
                <CardBody className="ribbon">
                  <div className="ribbon-box orange">8</div>
                  <Link to="/hr-events" className="my_sort_cut text-muted">
                    <i className="icon-doc" />
                    <span>{t("dashboard.newTicket")}</span>
                  </Link>
                </CardBody>
              </Card>
            </Col>
            <Col md={4} xl={2} className="col-6">
              <Card>
                <CardBody>
                  <Link to="/hr-accounts" className="my_sort_cut text-muted">
                    <i className="icon-calculator" />
                    <span>{t("dashboard.accounts")}</span>
                  </Link>
                </CardBody>
              </Card>
            </Col>
            <Col md={4} xl={2} className="col-6">
              <Card>
                <CardBody>
                  <Link to="/hr-report" className="my_sort_cut text-muted">
                    <i className="icon-pie-chart" />
                    <span>{t("dashboard.reports")}</span>
                  </Link>
                </CardBody>
              </Card>
            </Col>
          </Row>
          {location.pathname === "/dashboard/task" ? (
            <DashboardTasks
              pageNumber={pageNumber}
              setPageNumber={setPageNumber}
            />
          ) : location.pathname === "/dashboard/todo" ? (
            <Taskboard userRole={userRole} />
          ) : null}
        </Container>
        <Toast />
      </div>
    </>
  );
};

export default AdminDashboard;
