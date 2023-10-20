import React, {useEffect, useState} from "react";
import {getUserProfile} from "../../../redux/actions/usersAction";
import RecruiterDashboard from "./RecruiterDashboard";
import AdminDashboard from "./AdminDashboard";
import {useDispatch} from "react-redux";
import {TaskBoardLoader} from "../../constants/loaders";

const Dashboard = (props) => {
    const dispatch = useDispatch();
    const [userRole, setUserRole] = useState([]);

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
                <RecruiterDashboard/>
            ) : userRole.includes("Super Admin") || userRole.includes("Main Admin") || userRole.includes("Admin") ? (
                <AdminDashboard/>
            ) : <TaskBoardLoader/>}
        </>
    );
};

export default Dashboard;
