Ext.require([
    'Ext.tree.*',
    'Ext.data.*',
    'Ext.window.MessageBox'
]);

Ext.onReady(function() {
    var store = Ext.create('Ext.data.TreeStore', {
        proxy: {
            type: 'ajax',
            url: '/monitor.php?action=getdata'
        },
        sorters: [{
            property: 'leaf',
            direction: 'ASC'
        }, {
            property: 'text',
            direction: 'ASC'
        }]
    });
    
    var tree = Ext.create('Ext.tree.Panel', {
        store: store,
        rootVisible: false,
        useArrows: true,
        frame: true,
        title: 'Counter',
        //renderTo: 'tree-div',
        width: 440,
        //height: 500,
        layout: 'fit',
        border: 0,
        margins: '0 5 5 0',
        dockedItems: [{
            xtype: 'toolbar',
            items: [
            {
                xtype: 'button',
                text: 'Reset',
                handler: function(){
                    store.proxy.url = '/monitor.php?action=getdata';
                    store.reload();
                }
            },
            '->',
            {
                xtype: 'button',
                text: 'Save as a solution',
                handler: function(){
                    if(tree.getView().getChecked().length == 0){
                        Ext.MessageBox.show({
                            title: 'Save',
                            msg: 'no items selected.',
                            icon: Ext.MessageBox.INFO
                        });
                        return;
                    }
                    Ext.MessageBox.prompt('Save', 'Enter the solution name.', saveSolution);
                }
            }]
        }],
        listeners: {
            'resize': function(width, height, oldWidth, oldHeight, eOpts ) {
                //console.log('resize.' + tree.getHeight());
                if(border_panel != undefined && tree.getHeight() > 500)
                    border_panel.setHeight(tree.getHeight() + 70);
            }
        }
    });
    
    function saveSolution(btn, text){
        if(btn != 'ok') {
            return;
        }
        if(text == '' ) {
            Ext.MessageBox.show({
                title: 'Save',
                msg: 'Enter the solution name.',
                icon: Ext.MessageBox.ERROR
            });
            return;
        }
        var records = tree.getView().getChecked();
        host_ids = [];
        item_ids = [];
        item_name = [];
        
        Ext.Array.each(records, function(rec){
            host_ids.push(rec.raw.hostid);
            item_ids.push(rec.get('id'));
            item_name.push(rec.get('text'));
        });
        
        Ext.Ajax.request({
            url : '/monitor.php?action=save',
            method:'POST',
            success : function(re, op) {
                Ext.Msg.alert('Save', 'Save ' + text + ' solution sucess.');
                solution_store.reload();
            },
            failure : function(re, op) {
                Ext.Msg.alert('Save', 'Save ' + text + ' solution failure.');
            },
            params: {
                'solution': text,
                'hostid[]': host_ids,
                'data[]': item_ids,
                'name[]': item_name
                
            }
        });
    }
    
    var solution_store = Ext.create('Ext.data.Store', {
        autoLoad: true,
        fields:['solution', 'date'],
        proxy: {
            type: 'ajax',
            url: '/monitor.php?action=getsolution',
            reader: {
                type: 'json',
                root: 'items'
            }
        }
    });

        
    var deleteAction = Ext.create('Ext.Action', {
        text: 'Delete',
        handler: function(widget, event) {
            var rec = list_panel.getSelectionModel().getSelection()[0];
            if (rec) {
                Ext.Ajax.request({
                    url : '/monitor.php?action=delete',
                    method:'POST',
                    success : function(re, op) {
                        Ext.Msg.alert('Delete', 'Delete ' + rec.get('solution') + ' solution sucess.');
                        solution_store.reload();
                    },
                    failure : function(re, op) {
                        Ext.Msg.alert('Delete', 'Delete ' + rec.get('solution') + ' solution failure.');
                    },
                    params: {
                        'solution': rec.get('solution'),
                    }
                });
            }
        }
    });
    
    var contextMenu = Ext.create('Ext.menu.Menu', {
        items: [
            deleteAction
        ]
    });
    
    var list_panel = Ext.create('Ext.grid.Panel', {
        title: 'Solution',
        store: solution_store,
        columns: [
            { text: 'solution',  dataIndex: 'solution', width: 140 },
            { text: 'date', dataIndex: 'date', width: 150 }
        ],
        region:'west',
        margins: '0 5 5 5',
        collapsible: true,
        layout: 'fit',
        height: 200,
        width: 300,
        listeners: {
            'itemclick': function(grid, record, item, index, e, eOpts ) {
                store.proxy.url = '/monitor.php?action=getsolutionitems&solution=' + record.get('solution');
                store.reload();
            }
        },
        viewConfig: {
            stripeRows: true,
            listeners: {
                itemcontextmenu: function(view, rec, node, index, e) {
                    e.stopEvent();
                    contextMenu.showAt(e.getXY());
                    return false;
                }
            }
        }
    });

    
    var border_panel = Ext.create('Ext.panel.Panel', {
        width: 750,
        height: 300,
        //title: 'Border Layout',
        layout: 'border',
        items: [
        
        {
            //north south
            region: 'north',     // position for region
            xtype: 'label',
            height: 50,
            margins: '5 5 5 5',
            text: ' 选择Counter中需要监控的计数器，点击右上角Save solution，保存成功后会在左侧显示，点击左侧Solution列表可查看所包含的计数器。\n  保存时如果同名，则覆盖之前的Solution。',
            layout: 'fit'
        },
        list_panel,
        /*
        {
            title: 'Solution',
            region:'west',
            xtype: 'panel',
            margins: '5 0 0 5',
            width: 200,
            height: 300,
            collapsible: true,   // make collapsible
            id: 'west-region-container',
            layout: 'fit'
        },*/
        tree],
        renderTo: Ext.getBody()
    });
    
    
});
