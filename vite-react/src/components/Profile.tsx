import { useState, useEffect } from "react";
import axios from "axios";
import jQuery from 'jquery';

const api = axios.create({
  baseURL: "http://localhost:3000/graphql",
  headers: {'Accept': 'application/json',
          'Content-Type': 'application/json',}
})

const mapi = axios.create({
  baseURL: "http://localhost:3000/graphql",
  headers: {'Accept': 'multipart/form-data',
          'Content-Type': 'multipart/form-data',}
})


export default function Profile() {    
    const [userid, setUserid] = useState<string>('');;
    const [lname, setLname] = useState<string>('');
    const [fname, setFname] = useState<string>('');
    const [email, setEmail] = useState<string>('');
    const [mobile, setMobile] = useState<string>('');
    const [userpicture, setUserpicture] = useState<string>('');
    const [token, setToken] = useState<string>('');
    const [newpassword, setNewPassword ] = useState<string>('');
    const [confnewpassword, setConfNewPassword ] = useState<string>('');    
    const [profileMsg, setProfileMsg] = useState<string>('');
    const [showmfa, setShowMfa] = useState<boolean>(false);
    const [showpwd, setShowPwd] = useState<boolean>(false);
    const [showupdate, setShowUpdate] = useState<boolean>(false);
    // const [qrcodeurl, setQrcodeurl] = useState<Blob>(new Blob());
    const [qrcodeurl, setQrcodeurl] = useState<string>('');

    const fetchUserData = async (id: any, token: any) => {
        const getuseridQuery = {
            query: `
                query GetUserId($id: Float!, $token: String!) {
                    getUserById(id: $id, token: $token) {
                        id
                        firstname
                        lastname
                        email
                        mobile
                        userpic
                        qrcodeurl
                    }
                }
            `,
            variables: { id: Number(id), token: token }
        };

        try {
            const res = await api.post('', getuseridQuery, {
                headers: {
                    Authorization: `Bearer ${token}`
                 }
            });

            if (res.data.errors) {
                setProfileMsg(res.data.errors[0].message);
                return;
            }

            const userData = res.data.data?.getUserById;
            
            if (!userData) {
                setProfileMsg("User not found");
                return;
            }

            // Handle success
            setFname(userData.firstname);
            setLname(userData.lastname);
            setEmail(userData.email);
            setMobile(userData.mobile);
            let userpic = `http://localhost:3000/users/${userData.userpic}`;
            setUserpicture(userpic);            
            if (userData.qrcodeurl != null) {
                setQrcodeurl(userData.qrcodeurl);
            } else {
                setQrcodeurl('http://localhost:3000/images/qrcode.png');
            }

        } catch (error: any) {
            // Handles network errors or 4xx/5xx status codes
            const errorMsg = error.response?.data?.errors?.[0]?.message || error.message;
            setProfileMsg(errorMsg);
            setTimeout(() => setProfileMsg(''), 3000);
        }
    };

    useEffect(() => {
        jQuery("#password").prop('disabled', true);

        const userId = sessionStorage.getItem('USERID');
        if (userId != null) {
            setUserid(userId)
        } else {
            setUserid('')
        }
        const xtoken = sessionStorage.getItem('TOKEN');
        if (xtoken !== null) {
            setToken(xtoken);
        } else {
            setToken('');
        }
        setProfileMsg('please wait..');
        setTimeout(() => {
            fetchUserData(userId, xtoken);
            setProfileMsg('');
        }, 1000);
    },[]) 

    const submitProfile = async (event: any) => {
        event.preventDefault();
        const profileQuery = {
            query: `
                mutation UpdateProfile(
                    $id: Int!, 
                    $token: String!
                    $firstname: String!,
                    $lastname: String!,
                    $mobile: String!) {
                    profileUpdate(id: $id, token: $token, firstname: $firstname, lastname: $lastname, mobile: $mobile) {
                        message
                        id
                    }
                }
            `,
            variables: { 
                id: parseInt(userid), // Ensure it's an integer for GraphQL Int
                firstname: fname,
                lastname: lname,
                mobile: mobile,
                token: token
            }
        };

        try {
            const res = await api.post('', profileQuery, {
                headers: {
                    Authorization: `Bearer ${token}`
                 }
            });     

            if (res.data.errors) {
                setProfileMsg(res.data.errors[0].message);
                return;
            }

            const result = res.data.data?.profileUpdate;
            if (result?.message) {
                setProfileMsg(result.message);
                setTimeout(() => setProfileMsg(''), 3000);
            }
        } catch (error: any) {
            const errorMsg = error.response?.data?.errors?.[0]?.message || error.message;
            setProfileMsg(errorMsg);
            setTimeout(() => setProfileMsg(''), 3000);
        }
    }

    const changePicture = async (event: any) => {
        event.preventDefault();
        const file = event.target.files[0];
        if (!file) return;
        var pix = URL.createObjectURL(event.target.files[0]);
        jQuery('#userpic').attr('src', pix);

        const operations = JSON.stringify({
            query: `
                mutation UploadPicture($id: Int!, $token: String!, $userpic: Upload!) {
                    profilepicUpload(id: $id, token: $token, userpic: $userpic) {
                        message
                        id
                    }
                }
            `,
            variables: { id: parseInt(userid), token: token, userpic: null }
        });

        const map = JSON.stringify({ "0": ["variables.userpic"] });

        try {
            const formData = new FormData();
            formData.append("operations", operations);
            formData.append("map", map);
            formData.append("0", file); 

            const res = await mapi.post('', formData, {
                headers: {
                    'apollo-require-preflight': 'true',
                    'Accept': 'application/json',
                    Authorization: `Bearer ${token}`
                 }
            });

            if (res.data.errors) {
                setProfileMsg(res.data.errors[0].message);
            } else {
                setProfileMsg(res.data.data.profilepicUpload.message);
            }
            setTimeout(() => setProfileMsg(''), 3000);
        } catch (error: any) {
            const errorMsg = error.response?.data?.errors?.[0]?.message || error.message;
            setProfileMsg(errorMsg);
            setTimeout(() => setProfileMsg(''), 3000);
        }
    };

    const cpwdCheckbox = (e: any) => {
        if (e.target.checked) {
            setShowUpdate(true);
            setShowPwd(true);
            setShowMfa(false);
            jQuery('#checkTwoFactor').prop('checked', false);
            return;
        } else {
            setNewPassword('');
            setConfNewPassword('');
            setShowPwd(false);
            setShowUpdate(false)
        }
    }

    const mfaCheckbox = (e: any) => {
        if (e.target.checked) {
            setShowMfa(true);
            setShowUpdate(true)
            setShowPwd(false);
            jQuery('#checkChangePassword').prop('checked', false);
            return;
        } else {
            setShowMfa(false);
            setShowUpdate(false)
        }
    }

    const enableMFA = async () => {
        const mfaQuery = {
            query: `
                mutation ActivateMFA(
                    $id: Int!, 
                    $token: String!,
                    $TwoFactorEnabled: Boolean!) {
                    mfaActivation(id: $id, token: $token, TwoFactorEnabled: $TwoFactorEnabled) {
                        message
                        id
                        qrcodeurl
                    }
                }
            `,
            variables: { id: parseInt(userid), token: token, TwoFactorEnabled: true}
        };

        try {
            const res = await api.post('', mfaQuery, {
                headers: {
                    Authorization: `Bearer ${token}`
                 }
            });
            if (res.data.errors) {
                setProfileMsg(res.data.errors[0].message);
                return;
            }

            const result = res.data.data?.mfaActivation;
            console.log(result);

            if (result?.message) {
                setProfileMsg(result.message);
                setTimeout(() => {
                    setProfileMsg('');
                    setQrcodeurl(result.qrcodeurl);
                }, 3000);
            }
        } catch (error: any) {
            const errorMsg = error.response?.data?.errors?.[0]?.message || error.message;
            setProfileMsg(errorMsg);
            setTimeout(() => setProfileMsg(''), 3000);
        }
    }

    const disableMFA = async () => {
        const mfaQuery = {
            query: `
                mutation ActivateMFA(
                    $id: Int!, 
                    $token: String!,
                    $TwoFactorEnabled: Boolean!) {
                    mfaActivation(id: $id, token: $token, TwoFactorEnabled: $TwoFactorEnabled) {
                        message
                        id
                    }
                }
            `,
            variables: { id: parseInt(userid), token: token, TwoFactorEnabled: false}
        };

        try {
            const res = await api.post('', mfaQuery, {
                headers: {
                    Authorization: `Bearer ${token}`
                 }
            });
            if (res.data.errors) {
                setProfileMsg(res.data.errors[0].message);
                setQrcodeurl('/images/qrcode.png');
                setTimeout(() => setProfileMsg(''), 3000);
                return;
            }

            const result = res.data.data?.mfaActivation;
            if (result?.message) {
                console.log(result.qrcodeurl);
                setProfileMsg(result.message);
                setTimeout(() => {
                    setProfileMsg('');
                }, 3000);
            }
        } catch (error: any) {
            const errorMsg = error.response?.data?.errors?.[0]?.message || error.message;
            setProfileMsg(errorMsg);
            setTimeout(() => {
                setProfileMsg('');
            }, 3000);
        }
    }

    const changePassword = async (event: any) => {
        event.preventDefault();
        if (newpassword === '') {
            setProfileMsg("Please enter new Pasword.");
            setTimeout(() => {
                setProfileMsg('');
            },3000);
            return;
        }
        if (confnewpassword === '') {
            setProfileMsg("Please enter new Pasword confirmation.");
            setTimeout(() => {
                setProfileMsg('');
            },3000);
            return;            
        }

        if (newpassword !== confnewpassword) {
            setProfileMsg("new Password does not matched.");
            setTimeout(() => {
                setProfileMsg('');
            },3000);
            return;            
        }

        const passwordQuery = {
            query: `
                mutation ChangePassword($id: Int!, $token: String!, $password: String!) {
                    updateUserPassword(id: $id, token: $token, password: $password) {
                        message
                        id
                    }
                }
            `,
            variables: { 
                id: parseInt(userid),
                token: token,
                password: newpassword 
            }
        };

        try {
            const res = await api.post('', passwordQuery, {
                headers: {
                    Authorization: `Bearer ${token}`
                 }
            });
            
            // Handle GraphQL Errors (e.g., User Not Found)
            if (res.data.errors) {
                setProfileMsg(res.data.errors[0].message);
                return;
            }

            const result = res.data.data?.updateUserPassword;
            if (result?.message) {
                setProfileMsg(result.message);
                setTimeout(() => setProfileMsg(''), 3000);
            }
        } catch (error) {
            // Handle Network/Server Errors
            setProfileMsg("An unexpected error occurred.");
        }
    }

    return (
      <div className='profile-bg'>
        <div className="card card-profile mt-3">
        <div className="card-header bg-primary">
            <h3 className="text-white">User Profile ID No. {userid}</h3>
        </div>
        <div className="card-body">
        <form encType="multipart/form-data" autoComplete='false'>
                <div className='row'>
                    <div className='col'>
                        <input className="form-control bg-warning text-dark border-primary" id="firstname" name="firstname" type="text" value={fname} onChange={e => setFname(e.target.value)} required  />
                        <input className="form-control bg-warning text-dark border-primary mt-2" id="lastname" name="lastname" type="text" value={lname} onChange={e => setLname(e.target.value )} required />
                    </div>
                    <div className='col text-right'>

                    </div>
                </div>
                <div className='row'>
                    <div className='col'>
                        <input className="form-control bg-warning border-primary mt-2" id="email" name="email" type="email" value={email} onChange={e => setEmail(e.target.value)} readOnly />
                    </div>
                    <div className='col'>
                        {
                            userpicture == null ? (
                                <img id="userpic" className="userpic" alt="" />
                            )
                            :
                            <img id="userpic" src={userpicture} className="userpic" alt="" />
                        }
                    </div>
                </div>


                <div className='row'>
                    <div className='col'>
                            <input className="form-control bg-warning border-primary mt-2" id="mobileno" name="mobileno" type="text" value={mobile} onChange={e => setMobile(e.target.value)} required />
                    </div>
                    <div className='col'>
                        <input className="userpicture mt-2" onChange={changePicture} type="file"/>
                    </div>
                </div>

                <div className='row'>
                    {/* 2-FACTOR AUTHENTICATION */}
                    <div className='col'>
                            <div className="form-check mt-2">
                                <input onChange={mfaCheckbox} className="form-check-input chkbox" type="checkbox" id="checkTwoFactor"/>
                                <label className="form-check-label" htmlFor="checkTwoFactor">
                                    Enable 2-Factor Authentication
                                </label>
                            </div>
                            {
                                showmfa === true ? (
                                    <div className='row'>
                                        <div className='col-5'>
                                            <img id="googleAuth" src={qrcodeurl} className="qrCode2" alt="QRCODE" />
                                        </div>
                                        <div className='col-7'>
                                            <p className='text-danger mfa-pos-1'><strong>Requirements</strong></p>
                                            <p className="mfa-pos-2">You need to install <strong>Google or Microsoft Authenticator</strong> in your Mobile Phone, once installed, click Enable Button below, and <strong>SCAN QR CODE</strong>, next time you login, another dialog window will appear, then enter the <strong>OTP CODE</strong> from your Mobile Phone in order for you to login.</p>
                                            <button onClick={enableMFA} type="button" className='btn btn-primary mfa-btn-1 mx-1'>enable</button>
                                            <button onClick={disableMFA} type="button" className='btn btn-secondary mfa-btn-2'>disable</button>
                                        </div>
                                    </div>
                                )
                                :
                                null
                            }

                    </div>
                    <div className='col'>
                            {/* CHANGE PASSWORD */}
                            <div className="form-check mt-2">
                            <input onChange={cpwdCheckbox} className="form-check-input chkbox" type="checkbox" id="checkChangePassword"/>
                            <label className="form-check-label" htmlFor="checkChangePassword">
                                Change Password
                            </label>
                        </div>
                        { showpwd === true ? (
                            <>
                            <input className="form-control text-dark border-primary mt-2" type="password" id="newPassword" value={newpassword} onChange={e => setNewPassword(e.target.value)} placeholder='enter new Password'/>
                            <input className="form-control text-dark border-primary mt-1" type="password" id="confNewPassword" value={confnewpassword} onChange={e => setConfNewPassword(e.target.value)} placeholder='confirm new Password'/>
                            <button onClick={changePassword} className='btn btn-primary mt-2' type="button">change password</button>
                            </>
                        )
                        :
                            null
                        }

                    </div>
                </div> 
                {
                    showupdate === false ? (
                        <button onClick={submitProfile} type='submit' className='btn btn-primary text-white mt-2'>update profile</button>
                    )
                    :
                    null
                }
                </form>
        </div>
        <div className="card-footer text-danger">
            {profileMsg}
        </div>
        </div>
    </div>    
  )
}
