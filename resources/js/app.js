import "./bootstrap";
import ReactDOM from "react-dom/client";
import React from "react";
import { useNavigate } from "react-router-dom";
import { BrowserRouter as Router, Route, Routes } from "react-router-dom";
import Header from "./components/Header";
import Tasks from "./components/tasks/Tasks";
import Create from "./components/tasks/Create";
import Edit from "./components/tasks/Edit";
import Login from "./components/auth/Login";
import MasterLayout from "./components/Layout/MasterLayout";
import AuthLayout from "./components/Layout/AuthLayout";
import Register from "./components/auth/Register";
import ProtectedRoute from "./components/auth/ProtectedRoute";
import AuthRedirectRoute from "./components/auth/AuthRedirectRoute";

const rootElement = document.getElementById("app"); // Select the root div
const root = ReactDOM.createRoot(rootElement); // Use the root div instead of document.body
const token = localStorage.getItem("token");
root.render(
    <Router>
        <Routes>
            {/* Routes with MainLayout */}

            <Route
                element={
                    <ProtectedRoute>
                        <MasterLayout />
                    </ProtectedRoute>
                }
            >
                <Route path="/" element={<Tasks />} />
                <Route path="/home" element={<Tasks />} />
                <Route path="/create" element={<Create />} />
                <Route path="/task/edit/:id" element={<Edit />} />
            </Route>

            <Route
                element={
                    <AuthRedirectRoute>
                        <AuthLayout />
                    </AuthRedirectRoute>
                }
            >
                <Route path="/register" element={<Register />} />
                <Route path="/login" element={<Login />} />
            </Route>

            {/* Routes with AuthLayout */}
        </Routes>
    </Router>
);

// ReactDOM.createRoot(document.getElementById("app")).render();
