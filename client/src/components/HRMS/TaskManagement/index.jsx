import React, { useEffect, useState } from "react";
import Taskboard from "./Taskboard";
import { getUserProfile } from "../../../redux/actions/usersAction";
import { useDispatch } from "react-redux";
import DashboardTasks from "../Dashboard/DashboardTasks";
import { Container } from "reactstrap";
import { DataLoader, TaskBoardLoader } from "../../constants/loaders";

const TaskManagement = () => {
  const dispatch = useDispatch();

  const [userRole, setUserRole] = useState("");
  const [pageNumber, setPageNumber] = useState(1);

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

  return (
    <>
      {userRole.includes("Recruiter") ? (
        <Container fluid>
          <DashboardTasks
            pageNumber={pageNumber}
            setPageNumber={setPageNumber}
          />
        </Container>
      ) : userRole.includes("Super Admin") ||
        userRole.includes("Main Admin") ||
        userRole.includes("Admin") ? (
        <Taskboard userRole={userRole} />
      ) : (
        <div className={"text-center"}>
          <TaskBoardLoader />
        </div>
      )}
    </>
  );
};

export default TaskManagement;
