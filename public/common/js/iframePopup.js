$(document).ready(function () {

    //on frontend
    //open my account on profile tab
    $("#editLead").on('show.bs.modal', function (e) {
        var parent = $(e.relatedTarget);
        var height = parent.attr('data-height');
        var url = parent.attr('data-url');
        var width = parent.attr('data-width');
        $("#editLead iframe").attr(
                {
                    'src': url,
                    'height': height,
                    'width': width
                }
        );
    });



    $("#noteFrame").on('show.bs.modal', function (e) {
        var parent = $(e.relatedTarget);
        var height = parent.attr('data-height');
        var url = parent.attr('data-url');
        var width = parent.attr('data-width');
        $("#noteFrame iframe").attr(
                {
                    'src': url,
                    'height': height,
                    'width': width
                }
        );
    });

    $("#pickLead").on('show.bs.modal', function (e) {
        var parent = $(e.relatedTarget);
        var height = parent.attr('data-height');
        var url = parent.attr('data-url');
        var width = parent.attr('data-width');
        $("#pickLead iframe").attr(
                {
                    'src': url,
                    'height': height,
                    'width': width
                }
        );
    });


    $("#appStatusFrame").on('show.bs.modal', function (e) {
        var parent = $(e.relatedTarget);
        var height = parent.attr('data-height');
        var url = parent.attr('data-url');
        var width = parent.attr('data-width');
        $("#appStatusFrame iframe").attr(
                {
                    'src': url,
                    'height': height,
                    'width': width
                }
        );
    });

    $("#assignCaseFrame").on('show.bs.modal', function (e) {
        var parent = $(e.relatedTarget);
        var height = parent.attr('data-height');
        var url = parent.attr('data-url');
        var width = parent.attr('data-width');
        $("#assignCaseFrame iframe").attr(
                {
                    'src': url,
                    'height': height,
                    'width': width
                }
        );
    });

    $("#addCaseNote").on('show.bs.modal', function (e) {
        var parent = $(e.relatedTarget);
        var height = parent.attr('data-height');
        var url = parent.attr('data-url');
        var width = parent.attr('data-width');
        $("#addCaseNote iframe").attr(
                {
                    'src': url,
                    'height': height,
                    'width': width
                }
        );
    });
    $("#addAnchorFrm").on('show.bs.modal', function (e) {
        var parent = $(e.relatedTarget);
        var height = parent.attr('data-height');
        var url = parent.attr('data-url');
        var width = parent.attr('data-width');
        $("#addAnchorFrm iframe").attr(
                {
                    'src': url,
                    'height': height,
                    'width': width
                }
        );
    });

    $("#uploadAnchLead").on('show.bs.modal', function (e) {
        var parent = $(e.relatedTarget);
        var height = parent.attr('data-height');
        var url = parent.attr('data-url');
        var width = parent.attr('data-width');
        $("#uploadAnchLead iframe").attr(
                {
                    'src': url,
                    'height': height,
                    'width': width
                }
        );
    });

    $("#editAnchorFrm").on('show.bs.modal', function (e) {
        var parent = $(e.relatedTarget);
        var height = parent.attr('data-height');
        var url = parent.attr('data-url');
        var width = parent.attr('data-width');
        $("#editAnchorFrm iframe").attr(
                {
                    'src': url,
                    'height': height,
                    'width': width
                }
        );
    });


    $("#sendNextstage").on('show.bs.modal', function (e) {
        var parent = $(e.relatedTarget);
        var height = parent.attr('data-height');
        var url = parent.attr('data-url');
        var width = parent.attr('data-width');
        $("#sendNextstage iframe").attr(
                {
                    'src': url,
                    'height': height,
                    'width': width
                }
        );
    });

    $("#addRoleFrm").on('show.bs.modal', function (e) {
        var parent = $(e.relatedTarget);
        var height = parent.attr('data-height');
        var url = parent.attr('data-url');
        var width = parent.attr('data-width');
        $("#addRoleFrm iframe").attr(
                {
                    'src': url,
                    'height': height,
                    'width': width
                }
        );
    });

    $("#manageUserRole").on('show.bs.modal', function (e) {
        var parent = $(e.relatedTarget);
        var height = parent.attr('data-height');
        var url = parent.attr('data-url');
        var width = parent.attr('data-width');
        $("#manageUserRole iframe").attr(
                {
                    'src': url,
                    'height': height,
                    'width': width
                }
        );
    });

    $("#addmanageUserRole").on('show.bs.modal', function (e) {
        var parent = $(e.relatedTarget);
        var height = parent.attr('data-height');
        var url = parent.attr('data-url');
        var width = parent.attr('data-width');
        $("#addmanageUserRole iframe").attr(
                {
                    'src': url,
                    'height': height,
                    'width': width
                }
        );
    });

    $("#uploadSanctionLetter").on('show.bs.modal', function (e) {
        var parent = $(e.relatedTarget);
        var height = parent.attr('data-height');
        var url = parent.attr('data-url');
        var width = parent.attr('data-width');
        $("#uploadSanctionLetter iframe").attr(
                {
                    'src': url,
                    'height': height,
                    'width': width
                }
        );
    });

    $("#modalPromoter").on('show.bs.modal', function (e) {
        var parent = $(e.relatedTarget);
        var height = parent.attr('data-height');
        var url = parent.attr('data-url');
        var width = parent.attr('data-width');
        $("#modalPromoter iframe").attr(
                {
                    'src': url,
                    'height': height,
                    'width': width
                }
        );
    });

    $("#modalPromoter1").on('show.bs.modal', function (e) {
        var parent = $(e.relatedTarget);
        var height = parent.attr('data-height');
        var url = parent.attr('data-url');
        var width = parent.attr('data-width');
        $("#modalPromoter1 iframe").attr(
                {
                    'src': url,
                    'height': height,
                    'width': width
                }
        );
    });

    $("#modalPromoter2").on('show.bs.modal', function (e) {
        var parent = $(e.relatedTarget);
        var height = parent.attr('data-height');
        var url = parent.attr('data-url');
        var width = parent.attr('data-width');
        $("#modalPromoter2 iframe").attr(
                {
                    'src': url,
                    'height': height,
                    'width': width
                }
        );
    });

    $("#modalPromoter3").on('show.bs.modal', function (e) {
        var parent = $(e.relatedTarget);
        var height = parent.attr('data-height');
        var url = parent.attr('data-url');
        var width = parent.attr('data-width');
        $("#modalPromoter3 iframe").attr(
                {
                    'src': url,
                    'height': height,
                    'width': width
                }
        );
    });

    $("#modalPromoter7").on('show.bs.modal', function (e) {
        var parent = $(e.relatedTarget);
        var height = parent.attr('data-height');
        var url = parent.attr('data-url');
        var width = parent.attr('data-width');
        $("#modalPromoter7 iframe").attr(
                {
                    'src': url,
                    'height': height,
                    'width': width
                }
        );
    });

    $("#modalPromoter8").on('show.bs.modal', function (e) {
        var parent = $(e.relatedTarget);
        var height = parent.attr('data-height');
        var url = parent.attr('data-url');
        var width = parent.attr('data-width');
        $("#modalPromoter8 iframe").attr(
                {
                    'src': url,
                    'height': height,
                    'width': width
                }
        );
    });

    $("#assignFiFrame").on('show.bs.modal', function (e) {
        var parent = $(e.relatedTarget);
        var height = parent.attr('data-height');
        var url = parent.attr('data-url');
        var width = parent.attr('data-width');
        $("#assignFiFrame iframe").attr(
                {
                    'src': url,
                    'height': height,
                    'width': width
                }
        );
    });

    $("#pdNoteFrame").on('show.bs.modal', function (e) {
        var parent = $(e.relatedTarget);
        var height = parent.attr('data-height');
        var url = parent.attr('data-url');
        var width = parent.attr('data-width');
        $("#pdNoteFrame iframe").attr(
                {
                    'src': url,
                    'height': height,
                    'width': width
                }
        );
    });

         $("#modalPromoter").on('show.bs.modal', function (e) {
                var parent = $(e.relatedTarget);
                var height = parent.attr('data-height');
                var url = parent.attr('data-url');
                var width = parent.attr('data-width');
                $("#modalPromoter iframe").attr(
                        {
                            'src': url,
                            'height': height,
                            'width': width
                        }
                );
        });
        
        $("#modalPromoter1").on('show.bs.modal', function (e) {
                var parent = $(e.relatedTarget);
                var height = parent.attr('data-height');
                var url = parent.attr('data-url');
                var width = parent.attr('data-width');
                $("#modalPromoter1 iframe").attr(
                        {
                            'src': url,
                            'height': height,
                            'width': width
                        }
                );
        });
        
        $("#modalPromoter2").on('show.bs.modal', function (e) {
                var parent = $(e.relatedTarget);
                var height = parent.attr('data-height');
                var url = parent.attr('data-url');
                var width = parent.attr('data-width');
                $("#modalPromoter2 iframe").attr(
                        {
                            'src': url,
                            'height': height,
                            'width': width
                        }
                );
        });
        
        $("#modalPromoter3").on('show.bs.modal', function (e) {
                var parent = $(e.relatedTarget);
                var height = parent.attr('data-height');
                var url = parent.attr('data-url');
                var width = parent.attr('data-width');
                $("#modalPromoter3 iframe").attr(
                        {
                            'src': url,
                            'height': height,
                            'width': width
                        }
                );
        });
        
         $("#modalPromoter7").on('show.bs.modal', function (e) {
                var parent = $(e.relatedTarget);
                var height = parent.attr('data-height');
                var url = parent.attr('data-url');
                var width = parent.attr('data-width');
                $("#modalPromoter7 iframe").attr(
                        {
                            'src': url,
                            'height': height,
                            'width': width
                        }
                );
        });
        
         $("#modalPromoter8").on('show.bs.modal', function (e) {
                var parent = $(e.relatedTarget);
                var height = parent.attr('data-height');
                var url = parent.attr('data-url');
                var width = parent.attr('data-width');
                $("#modalPromoter8 iframe").attr(
                        {
                            'src': url,
                            'height': height,
                            'width': width
                        }
                );
        });
        
         $("#modalPromoter9").on('show.bs.modal', function (e) {
                var parent = $(e.relatedTarget);
                var height = parent.attr('data-height');
                var url = parent.attr('data-url');
                var width = parent.attr('data-width');
                $("#modalPromoter9 iframe").attr(
                        {
                            'src': url,
                            'height': height,
                            'width': width
                        }
                );
        });


        $("#assignFiFrame").on('show.bs.modal', function (e) {
                var parent = $(e.relatedTarget);
                var height = parent.attr('data-height');
                var url = parent.attr('data-url');
                var width = parent.attr('data-width');
                $("#assignFiFrame iframe").attr(
                        {
                            'src': url,
                            'height': height,
                            'width': width
                        }
                );
        });   
        
        $("#assignRcuFrame").on('show.bs.modal', function (e) {
            var parent = $(e.relatedTarget);
                var height = parent.attr('data-height');
                var url = parent.attr('data-url');
                var width = parent.attr('data-width');
                $("#assignRcuFrame iframe").attr(
                        {
                            'src': url,
                            'height': height,
                            'width': width
                        }
                );
        });

        $("#uploadFiDocFrame").on('show.bs.modal', function (e) {
                var parent = $(e.relatedTarget);
                var height = parent.attr('data-height');
                var url = parent.attr('data-url');
                var width = parent.attr('data-width');
                $("#uploadFiDocFrame iframe").attr(
                        {
                            'src': url,
                            'height': height,
                            'width': width
                        }
                );
        });               
    

        $("#uploadRcuDocFrame").on('show.bs.modal', function (e) {
                var parent = $(e.relatedTarget);
                var height = parent.attr('data-height');
                var url = parent.attr('data-url');
                var width = parent.attr('data-width');
                $("#uploadRcuDocFrame iframe").attr(
                        {
                            'src': url,
                            'height': height,
                            'width': width
                        }
                );
        });

        $("#addAgencyFrame").on('show.bs.modal', function (e) {
            var parent = $(e.relatedTarget);
                var height = parent.attr('data-height');
                var url = parent.attr('data-url');
                var width = parent.attr('data-width');
                $("#addAgencyFrame iframe").attr(
                        {
                            'src': url,
                            'height': height,
                            'width': width
                        }
                );
        });  

        $("#uploadDocument").on('show.bs.modal', function (e) {
                var parent = $(e.relatedTarget);
                var height = parent.attr('data-height');
                var url = parent.attr('data-url');
                var width = parent.attr('data-width');
                $("#uploadDocument iframe").attr(
                        {
                            'src': url,
                            'height': height,
                            'width': width
                        }
                );
        });

        $("#editAgencyFrame").on('show.bs.modal', function (e) {
                var parent = $(e.relatedTarget);
                var height = parent.attr('data-height');
                var url = parent.attr('data-url');
                var width = parent.attr('data-width');
                $("#editAgencyFrame iframe").attr(
                        {
                            'src': url,
                            'height': height,
                            'width': width
                        }
                );
        });

        $("#addAgencyUserFrame").on('show.bs.modal', function (e) {
                var parent = $(e.relatedTarget);
                var height = parent.attr('data-height');
                var url = parent.attr('data-url');
                var width = parent.attr('data-width');
                $("#addAgencyUserFrame iframe").attr(
                        {
                            'src': url,
                            'height': height,
                            'width': width
                        }
                );
        });

        $("#editAgencyUserFrame").on('show.bs.modal', function (e) {
                var parent = $(e.relatedTarget);
                var height = parent.attr('data-height');
                var url = parent.attr('data-url');
                var width = parent.attr('data-width');
                $("#editAgencyUserFrame iframe").attr(
                        {
                            'src': url,
                            'height': height,
                            'width': width
                        }
                );
        });

        $("#addChargesFrame").on('show.bs.modal', function (e) {
                var parent = $(e.relatedTarget);
                var height = parent.attr('data-height');
                var url = parent.attr('data-url');
                var width = parent.attr('data-width');
                $("#addChargesFrame iframe").attr(
                        {
                            'src': url,
                            'height': height,
                            'width': width
                        }
                );
        });

        $("#editChargesFrame").on('show.bs.modal', function (e) {
                var parent = $(e.relatedTarget);
                var height = parent.attr('data-height');
                var url = parent.attr('data-url');
                var width = parent.attr('data-width');
                $("#editChargesFrame iframe").attr(
                    {
                        'src': url,
                        'height': height,
                        'width': width
                    }
                );
        });

        $("#addDocumentsFrame").on('show.bs.modal', function (e) {
                var parent = $(e.relatedTarget);
                var height = parent.attr('data-height');
                var url = parent.attr('data-url');
                var width = parent.attr('data-width');
                $("#addDocumentsFrame iframe").attr(
                        {
                            'src': url,
                            'height': height,
                            'width': width
                        }
                );
        });

        $("#editDocumentsFrame").on('show.bs.modal', function (e) {
                var parent = $(e.relatedTarget);
                var height = parent.attr('data-height');
                var url = parent.attr('data-url');
                var width = parent.attr('data-width');
                $("#editDocumentsFrame iframe").attr(
                    {
                        'src': url,
                        'height': height,
                        'width': width
                    }
                );
        });
        
        $("#addIndustriesFrame").on('show.bs.modal', function (e) {
                var parent = $(e.relatedTarget);
                var height = parent.attr('data-height');
                var url = parent.attr('data-url');
                var width = parent.attr('data-width');
                $("#addIndustriesFrame iframe").attr(
                        {
                            'src': url,
                            'height': height,
                            'width': width
                        }
                );
        });

        $("#editIndustriesFrame").on('show.bs.modal', function (e) {
                var parent = $(e.relatedTarget);
                var height = parent.attr('data-height');
                var url = parent.attr('data-url');
                var width = parent.attr('data-width');
                $("#editIndustriesFrame iframe").attr(
                    {
                        'src': url,
                        'height': height,
                        'width': width
                    }
                );
        });
        
           // Entity
        $("#addEntityFrame").on('show.bs.modal', function (e) {
            var parent = $(e.relatedTarget);
            var height = parent.attr('data-height');
            var url = parent.attr('data-url');
            var width = parent.attr('data-width');
            $("#addEntityFrame iframe").attr(
                    {
                            'src': url,
                            'height': height,
                            'width': width
                    }
                );
         });
           
        $("#editEntityFrame").on('show.bs.modal', function (e) {
            var parent = $(e.relatedTarget);
            var height = parent.attr('data-height');
            var url = parent.attr('data-url');
            var width = parent.attr('data-width');
            $("#editEntityFrame iframe").attr(
                {
                    'src': url,
                    'height': height,
                    'width': width
                }
            );
        });
                
        $("#ppUploadDocument").on('show.bs.modal', function (e) {
                var parent = $(e.relatedTarget);
                var height = parent.attr('data-height');
                var url = parent.attr('data-url');
                var width = parent.attr('data-width');
                $("#ppUploadDocument iframe").attr(
                        {
                            'src': url,
                            'height': height,
                            'width': width
                        }
                );
        });

        $("#queryFrame").on('show.bs.modal', function (e) {
            var parent = $(e.relatedTarget);
                var height = parent.attr('data-height');
                var url = parent.attr('data-url');
                var width = parent.attr('data-width');
                $("#queryFrame iframe").attr(
                        {
                            'src': url,
                            'height': height,
                            'width': width
                        }
                );
        });

        $("#queryDeatailsFrame").on('show.bs.modal', function (e) {
                var parent = $(e.relatedTarget);
                var height = parent.attr('data-height');
                var url = parent.attr('data-url');
                var width = parent.attr('data-width');
                $("#queryDeatailsFrame iframe").attr(
                        {
                            'src': url,
                            'height': height,
                            'width': width
                        }
                );
        });
        
        $("#addDoaLevelFrame").on('show.bs.modal', function (e) {
            var parent = $(e.relatedTarget);
            var height = parent.attr('data-height');
            var url = parent.attr('data-url');
            var width = parent.attr('data-width');
            $("#addDoaLevelFrame iframe").attr(
                    {
                        'src': url,
                        'height': height,
                        'width': width
                    }
            );
        });
        
        $("#editDoaLevelFrame").on('show.bs.modal', function (e) {
            var parent = $(e.relatedTarget);
            var height = parent.attr('data-height');
            var url = parent.attr('data-url');
            var width = parent.attr('data-width');
            $("#editDoaLevelFrame iframe").attr(
                    {
                        'src': url,
                        'height': height,
                        'width': width
                    }
            );
        });
        
        $("#assignRoleLevelFrame").on('show.bs.modal', function (e) {
            var parent = $(e.relatedTarget);
            var height = parent.attr('data-height');
            var url = parent.attr('data-url');
            var width = parent.attr('data-width');
            $("#assignRoleLevelFrame iframe").attr(
                    {
                        'src': url,
                        'height': height,
                        'width': width
                    }
            );
        });          

        $("#limitOfferFrame").on('show.bs.modal', function (e) {
                var parent = $(e.relatedTarget);
                var height = parent.attr('data-height');
                var url = parent.attr('data-url');
                var width = parent.attr('data-width');
                $("#limitOfferFrame iframe").attr(
                        {
                            'src': url,
                            'height': height,
                            'width': width
                        }
                );
        });

        $("#editLimitFrame").on('show.bs.modal', function (e) {
                var parent = $(e.relatedTarget);
                var height = parent.attr('data-height');
                var url = parent.attr('data-url');
                var width = parent.attr('data-width');
                $("#editLimitFrame iframe").attr(
                        {
                            'src': url,
                            'height': height,
                            'width': width
                        }
                );
        });
        
         $("#modalInvoiceFailed").on('show.bs.modal', function (e) {
                var parent = $(e.relatedTarget);
                var height = parent.attr('data-height');
                var url = parent.attr('data-url');
                var width = parent.attr('data-width');
                $("#modalInvoiceFailed iframe").attr(
                        {
                            'src': url,
                            'height': height,
                            'width': width
                        }
                );
        });
        
          $("#modalInvoiceDisbursed").on('show.bs.modal', function (e) {
                var parent = $(e.relatedTarget);
                var height = parent.attr('data-height');
                var url = parent.attr('data-url');
                var width = parent.attr('data-width');
                $("#modalInvoiceDisbursed iframe").attr(
                        {
                            'src': url,
                            'height': height,
                            'width': width
                        }
                );
        });
        
         $("#add_bank_account").on('show.bs.modal', function (e) {
        var parent = $(e.relatedTarget);
        var height = parent.attr('data-height');
        var url = parent.attr('data-url');
        var width = parent.attr('data-width');
        var title = parent.attr('title');
        if(title){
               $('#add_bank_account').find('.modal-title').html(title); 
        }
        $("#add_bank_account iframe").attr(
                {
                    'src': url,
                    'height': height,
                    'width': width
                }
        );
    });
        
});